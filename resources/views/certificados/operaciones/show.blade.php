<x-base-layout>
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-secondary { background-color: #f1f5f9 !important; color: #475569 !important; border: none; }
        .bg-pastel-danger { background-color: #fee2e2 !important; color: #ef4444 !important; border: none; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .nav-tabs-custom .nav-link { border: none; color: #616161; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .nav-tabs-custom .nav-link.active { color: #0052cc; background: transparent; border-bottom: 3px solid #0052cc; }
        .fs-7 { font-size: 0.9rem; }
        .fs-8 { font-size: 0.8rem; }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }
        .hover-opacity:hover { opacity: 0.8; transition: opacity 0.2s; }
        .accordion-custom .accordion-button:not(.collapsed) { background-color: #f8fafc; color: #0f172a; box-shadow: none; }
        .accordion-custom .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,0.1); }
        .accordion-custom .accordion-button::after { background-size: 1.25rem; transition: all 0.3s ease; }
        .accordion-custom .accordion-item { border: 1px solid #e2e8f0; transition: all 0.3s ease; }
        .accordion-custom .accordion-item:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border-color: #cbd5e1; }
        .table-inner th { font-weight: 600; color: #64748b; font-size: 0.75rem; background-color: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px;}
        .cuota-badge { width: 38px; height: 38px; font-size: 1rem; background: linear-gradient(135deg, #4a90e2, #0052cc); color: white; box-shadow: 0 4px 6px rgba(0, 82, 204, 0.2); }
        .table-spreadsheet { border-collapse: collapse; }
        .table-spreadsheet th { font-size: 0.75rem; text-transform: uppercase; color: #64748b; border: 1px solid #e2e8f0; padding: 1rem 0.75rem; background-color: #f8fafc; }
        .table-spreadsheet td { padding: 0; border: 1px solid #e2e8f0; vertical-align: middle; }
        .input-spreadsheet { width: 100%; border: none; padding: 0.85rem 0.75rem; background: transparent; outline: none; font-size: 0.85rem; color: #0f172a; transition: all 0.2s; }
        .input-spreadsheet:focus { background-color: #f0fdf4; box-shadow: inset 0 0 0 2px #22c55e; }
        select.input-spreadsheet { cursor: pointer; appearance: auto; -webkit-appearance: auto; padding-right: 2rem; }
        .progress-minimalist { height: 6px; width: 100%; background-color: #fee2e2; border-radius: 4px; overflow: hidden; position: relative; }
        .progress-minimalist::before { content: ''; position: absolute; top: 0; left: -50%; width: 50%; height: 100%; background-color: #ef4444; animation: progress-slide 1.5s infinite ease-in-out; border-radius: 4px; }
        @keyframes progress-slide { 0% { left: -50%; width: 30%; } 50% { width: 60%; } 100% { left: 100%; width: 30%; } }
    </style>

    <div class="app-container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">

            <!-- ========================================== -->
            <!-- 1. TÍTULO Y NAVEGACIÓN                     -->
            <!-- ========================================== -->
            <div>
                <a href="{{ route('certificados.operaciones.index') }}" class="text-decoration-none text-muted d-inline-flex align-items-center fw-semibold hover-opacity mb-2" style="font-size: 0.85rem;">
                    <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-2 shadow-sm border" style="width: 28px; height: 28px;">
                        <i class="fas fa-chevron-left fs-8 text-secondary"></i>
                    </div>
                    Volver a la matriz
                </a>
                <h1 class="h3 fw-bold m-0 text-dark d-flex align-items-center gap-3">
                    Operación
                    <span class="badge bg-pastel-primary px-3 py-2 rounded-pill fs-6 shadow-sm border border-primary border-opacity-10">
                        # {{ $operacion->numero_radicado }}
                    </span>
                </h1>
            </div>

            <!-- ========================================== -->
            <!-- 2. BOTONES DE ACCIÓN MINIMALISTAS          -->
            <!-- ========================================== -->
            <div class="d-flex flex-wrap align-items-center gap-2">

                <!-- NUEVO BOTÓN INICIAL (Visual) -->
                <button type="button" class="btn bg-pastel-secondary shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity">
                    <i class="fas fa-cog me-2 opacity-75"></i> Parámetros
                </button>

                <!-- Acciones Secundarias (Fondos Pastel) -->
                <button type="button" class="btn bg-pastel-warning shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalTransicionar">
                    <i class="fas fa-exchange-alt me-2 opacity-75"></i> Estado
                </button>

                <button type="button" class="btn bg-pastel-info shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalAlerta">
                    <i class="fas fa-bell me-2 opacity-75"></i> Alerta
                </button>

                <button type="button" class="btn bg-pastel-danger shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalTipo">
                    <i class="fas fa-file-pdf me-2 opacity-75"></i> Certificado
                </button>

                <!-- Acción Principal (Fondo Sólido Destacado) -->
                <a href="{{ route('certificados.operaciones.informe_cliente', $operacion->id) }}" target="_blank" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center ms-md-2 hover-opacity">
                    <i class="fas fa-chart-line me-2"></i> Informe Cliente
                </a>

            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-4 col-lg-5">
                <div class="card card-custom shadow-sm mb-4 border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol-label bg-pastel-primary me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 15px;">
                                <i class="fas fa-user-tie text-primary fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">Datos del Cliente</h5>
                                <span class="text-muted fs-8">Información de Maestras</span>
                            </div>
                        </div>

                        @if($operacion->tercero)
                            <div class="bg-light rounded-4 p-3 mb-3">
                                <div class="fw-bolder text-dark fs-6">{{ $operacion->tercero->nom_ter }} {{ $operacion->tercero->apl1 }}</div>
                                <div class="text-muted fs-8 mt-1">NIT: {{ $operacion->tercero->cod_ter }}</div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-7">
                                <span class="text-muted"><i class="fas fa-phone-alt me-2 opacity-50"></i>Teléfono:</span>
                                <span class="fw-semibold text-dark">{{ $operacion->tercero->tel ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-7">
                                <span class="text-muted"><i class="fas fa-envelope me-2 opacity-50"></i>Email:</span>
                                <span class="fw-semibold text-dark text-truncate ms-2" style="max-width: 150px;" title="{{ $operacion->tercero->email }}">{{ $operacion->tercero->email ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between fs-7">
                                <span class="text-muted"><i class="fas fa-map-marker-alt me-2 opacity-50"></i>Ciudad:</span>
                                <span class="fw-semibold text-dark">{{ $operacion->tercero->ciudad ?? 'N/A' }}</span>
                            </div>
                        @else
                            <div class="alert bg-pastel-warning text-center border-0 rounded-4">Tercero no encontrado en maestras.</div>
                        @endif
                    </div>
                </div>
                <div class="card card-custom shadow-sm border-0">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase fw-bold text-muted mb-3 fs-8"><i class="fas fa-microchip me-2"></i> Info Técnica (ETL)</h6>
                        <div class="d-flex justify-content-between mb-2 fs-7">
                            <span class="text-muted">Número Radicado:</span>
                            <span class="fw-bold text-dark">{{ $operacion->numero_radicado }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-7">
                            <span class="text-muted">Lote / Bloque:</span>
                            <span class="badge bg-pastel-primary rounded-pill px-3">API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 fs-7">
                            <span class="text-muted">Fecha Creación Lote:</span>
                            <span class="fw-semibold text-dark">
                                {{ $operacion->created_at ? $operacion->created_at->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ======================================================================= -->
                <!-- CAJA PRINCIPAL: MATRIZ DE TRAZABILIDAD (AUDITORÍA)                      -->
                <!-- ======================================================================= -->
                <div class="card card-custom shadow-sm border-0 mt-4 h-100" style="max-height: 500px; display: flex; flex-direction: column;">

                    <!-- ------------------------------------------------------------------- -->
                    <!-- 1. CABECERA: Título de la tarjeta                                   -->
                    <!-- ------------------------------------------------------------------- -->
                    <div class="card-header bg-white pt-4 pb-3 border-bottom px-4">
                        <h6 class="text-uppercase fw-bold text-muted mb-0 fs-8">
                            <i class="fas fa-shield-alt me-2 text-primary"></i>
                            Matriz de Trazabilidad
                        </h6>
                    </div>

                    <!-- ------------------------------------------------------------------- -->
                    <!-- 2. CUERPO: Zona donde hacemos scroll y mostramos los datos          -->
                    <!-- ------------------------------------------------------------------- -->
                    <div class="card-body p-4 pt-3" style="overflow-y: auto;">

                        @if(isset($logsAuditoria) && $logsAuditoria->count() > 0)

                            <!-- CONTENEDOR GENERAL DE LA LÍNEA DE TIEMPO -->
                            <div class="position-relative">

                                <!-- El palito vertical de fondo (la línea que une los puntos) -->
                                <div class="position-absolute h-100 border-start border-2 border-secondary border-opacity-25" style="left: 11px; top: 0;"></div>

                                <!-- EMPIEZA EL BUCLE: Dibujamos cada evento uno por uno -->
                                @foreach($logsAuditoria as $log)

                                    <!-- BLOQUE DE UN SOLO EVENTO (ITEM) -->
                                    <div class="mb-4 position-relative ps-4 ms-2">

                                        <!-- La bolita azul que se pone sobre la línea vertical -->
                                        <span class="position-absolute bg-white border border-2 border-primary rounded-circle" style="width: 14px; height: 14px; left: -15px; top: 0.25rem;"></span>

                                        <!-- Fila que separa el contenido (Izquierda) de la IP (Derecha) -->
                                        <div class="d-flex justify-content-between align-items-start">

                                            <!-- COLUMNA IZQUIERDA: Textos principales -->
                                            <div>
                                                <!-- Nombre del evento -->
                                                <h6 class="fw-bold text-dark fs-7 mb-1">
                                                    {{ $log->tituloEvento }}
                                                </h6>

                                                <!-- Fecha y Origen -->
                                                <div class="text-muted fs-8 d-flex flex-wrap gap-2 align-items-center">
                                                    <span><i class="far fa-clock me-1"></i> {{ $log->fechaEvento }} </span>
                                                    <span>| <i class="fas fa-desktop me-1"></i> {{ $log->origenEvento }} </span>
                                                </div>

                                                <!-- Quién lo hizo (Usuario y Cargo) -->
                                                <div class="text-muted fs-8 mt-1">
                                                    <i class="fas fa-user-shield me-1 opacity-50"></i>
                                                    <strong class="text-dark">{{ $log->nombreUsuario }}</strong>
                                                    <span class="fst-italic opacity-75">({{ $log->cargoUsuario }})</span>
                                                </div>
                                            </div>

                                            <!-- COLUMNA DERECHA: Etiqueta con la IP -->
                                            <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25 fs-8" title="IP de origen">
                                                <i class="fas fa-network-wired me-1"></i> {{ $log->ipDelUsuario }}
                                            </span>

                                        </div>

                                        <!-- CAJA INFERIOR GRIS: Detalles extra (Solo si existen) -->
                                        @if($log->hayDetalles)
                                            <div class="mt-2 bg-light p-2 rounded-3 fs-8 font-monospace text-muted" style="border-left: 3px solid #cbd5e1;">
                                                @foreach($log->detalles_procesados as $llave => $valor)
                                                    <div>
                                                        <strong class="text-dark">{{ $llave }}:</strong> {{ $valor }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                    </div>
                                    <!-- FIN DEL BLOQUE DEL EVENTO -->

                                @endforeach
                                <!-- TERMINA EL BUCLE -->

                            </div>

                        @else
                            <!-- ------------------------------------------------------------------- -->
                            <!-- PANTALLA VACÍA: Se muestra si no hay historial                      -->
                            <!-- ------------------------------------------------------------------- -->
                            <div class="text-center py-4 text-muted bg-light rounded-4 border-dashed">
                                <i class="fas fa-clipboard-list fs-2 text-secondary mb-2 opacity-25"></i>
                                <p class="mb-0 fs-8 fw-semibold">No hay registros de auditoría.</p>
                            </div>
                        @endif

                    </div>
                </div>
                <!-- ======================================================================= -->
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card card-custom shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-0 border-bottom px-4" style="border-radius: 20px 20px 0 0;">
                        <ul class="nav nav-tabs nav-tabs-custom border-0 d-flex flex-nowrap overflow-auto" id="operacionTabs" role="tablist" style="scrollbar-width: none;">

                            <!-- Parámetros (NUEVO VISUAL) -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="parametros-tab" data-bs-toggle="tab" data-bs-target="#parametros" type="button" role="tab">
                                    <span class="bg-pastel-secondary text-dark rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-cog fs-8"></i>
                                    </span>
                                    Parámetros
                                </button>
                            </li>

                            <!-- Líneas -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active d-flex align-items-center" id="lineas-tab" data-bs-toggle="tab" data-bs-target="#lineas" type="button" role="tab">
                                    <span class="bg-pastel-primary text-primary rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-sitemap fs-8"></i>
                                    </span>
                                    Líneas
                                </button>
                            </li>

                            <!-- Alertas -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="alertas-tab" data-bs-toggle="tab" data-bs-target="#alertas" type="button" role="tab">
                                    <span class="bg-pastel-warning text-warning rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-bell fs-8"></i>
                                    </span>
                                    Alertas
                                </button>
                            </li>

                            <!-- Historial -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab">
                                    <span class="bg-pastel-secondary text-secondary rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-history fs-8"></i>
                                    </span>
                                    Historial
                                </button>
                            </li>

                            <!-- Certificados -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="certificados-tab" data-bs-toggle="tab" data-bs-target="#certificados" type="button" role="tab">
                                    <span class="bg-pastel-danger text-danger rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-file-pdf fs-8"></i>
                                    </span>
                                    Certificados
                                </button>
                            </li>

                            <!-- Gráficos -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="graficos-tab" data-bs-toggle="tab" data-bs-target="#graficos" type="button" role="tab">
                                    <span class="bg-pastel-info text-info rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-chart-pie fs-8"></i>
                                    </span>
                                    Gráficos
                                </button>
                            </li>

                            <!-- Operarios -->
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="operarios-tab" data-bs-toggle="tab" data-bs-target="#operarios" type="button" role="tab">
                                    <span class="bg-pastel-success text-success rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px;">
                                        <i class="fas fa-users-cog fs-8"></i>
                                    </span>
                                    Operarios
                                </button>
                            </li>

                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="operacionTabsContent">
                            {{-- TAB 0: PARÁMETROS (VISUAL) --}}
                            <div class="tab-pane fade" id="parametros" role="tabpanel">
                                <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed mt-2">
                                    <i class="fas fa-cogs fs-1 text-secondary mb-3 opacity-25"></i>
                                    <h6 class="fw-bold text-dark">Configuración y Parámetros</h6>
                                    <p class="mb-0 fs-7">Módulo en construcción. Aquí podrás gestionar los parámetros y reglas de negocio específicas para esta operación.</p>
                                </div>
                            </div>

                            {{-- TAB 1: LÍNEAS (Estilo Hoja de Cálculo Compacta) --}}
                            <div class="tab-pane fade show active" id="lineas" role="tabpanel">

                                @if($lineasAgrupadas->count() > 0)

                                    {{-- Mini-subtítulo Informativo --}}
                                    <div class="alert bg-light border rounded-3 mb-3 py-2 px-3 d-flex align-items-center shadow-sm" style="border-color: var(--c-border) !important;">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <span class="text-muted" style="font-size: 0.8rem;">
                                            El cliente <strong class="text-dark">{{ $operacion->tercero ? $operacion->tercero->nom_ter . ' ' . $operacion->tercero->apl1 : 'Seleccionado' }}</strong>
                                            tiene registradas <strong class="text-primary">{{ $lineasAgrupadas->count() }} {{ $lineasAgrupadas->count() == 1 ? 'línea' : 'líneas' }}</strong> en su historial.
                                        </span>
                                    </div>

                                    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover align-middle mb-0" style="font-size: 0.75rem;">

                                                @foreach($lineasAgrupadas as $nombreLinea => $datosLinea)
                                                    {{-- Fila Agrupadora (Usa un botón 100% ancho para asegurar el clic) --}}
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th colspan="8" class="p-0 border-bottom-0">
                                                                <button class="btn btn-light w-100 d-flex justify-content-between align-items-center rounded-0 px-3 py-2 shadow-none border-0 text-start collapsed"
                                                                        type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapseLinea-{{ $loop->index }}"
                                                                        aria-expanded="false"
                                                                        aria-controls="collapseLinea-{{ $loop->index }}"
                                                                        style="background-color: #f8f9fa;">

                                                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                                                        <i class="fas fa-chevron-down text-secondary me-2" style="font-size: 0.7rem;"></i>
                                                                        <i class="fas fa-layer-group text-primary me-2"></i>{{ $nombreLinea }} - Historial de Siasoft (Solo consulta)
                                                                    </span>
                                                                    <div class="d-flex gap-3 fw-normal text-muted" style="font-size: 0.7rem;">
                                                                        <span><i class="fas fa-file-invoice me-1"></i> {{ $datosLinea['count'] }} Facturas</span>
                                                                        <span class="fw-bold text-success"><i class="fas fa-dollar-sign me-1"></i> Total: ${{ number_format((float)$datosLinea['total'], 2) }}</span>
                                                                    </div>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    {{-- Cuerpo Colapsable (Todas arrancan cerradas con 'collapse' puro) --}}
                                                    <tbody id="collapseLinea-{{ $loop->index }}" class="collapse border-bottom" style="border-bottom-width: 2px !important; border-color: var(--c-border) !important;">

                                                        {{-- Cabeceras de las Columnas (Se ocultan junto con los datos) --}}
                                                        <tr class="bg-white text-muted text-uppercase" style="font-size: 0.65rem;">
                                                            <th class="px-3 py-2 border-bottom text-secondary" style="width: 12%;">N° Factura</th>
                                                            <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 5%;">Cuota</th>
                                                            <th class="px-2 py-2 border-bottom text-secondary" style="width: 15%;">Pagaré</th>
                                                            <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 12%;">F. Vencimiento</th>
                                                            <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 10%;">Días Mora</th>
                                                            <th class="px-3 py-2 border-bottom text-secondary text-end" style="width: 15%;">V. Inicial (Bruto)</th>
                                                            <th class="px-3 py-2 border-bottom text-secondary text-end" style="width: 15%;">V. a Pagar (Neto)</th>
                                                            <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 15%;">Estado</th>
                                                        </tr>

                                                        {{-- Filas de Datos --}}
                                                        @foreach($datosLinea['facturas'] as $factura)
                                                            <tr>
                                                                <td class="px-3 py-1 fw-bold text-dark" style="font-family: monospace;">{{ $factura->id_factura ?? 'N/A' }}</td>
                                                                <td class="px-2 py-1 text-center text-muted">{{ $factura->cuota ?? '-' }}</td>
                                                                <td class="px-2 py-1 text-muted">{{ $factura->pagare ?? 'S/N' }}</td>

                                                                <td class="px-2 py-1 text-center">
                                                                    @if($factura->fecha_venci) {{ $factura->fechaVFormateada }}
                                                                    @else <span class="text-muted opacity-50">-</span> @endif
                                                                </td>

                                                                <td class="px-2 py-1 text-center">
                                                                    @if($factura->diasMoraCalculados < 0) <span class="text-danger fw-bold">{{ abs(intval($factura->diasMoraCalculados)) }}</span>
                                                                    @else <span class="text-muted">0</span> @endif
                                                                </td>

                                                                <td class="px-3 py-1 text-end text-muted">${{ number_format((float)$factura->valor_inicial, 2) }}</td>
                                                                <td class="px-3 py-1 text-end fw-bold" style="color: #047857;">${{ number_format((float)$factura->valor, 2) }}</td>

                                                                <td class="px-2 py-1 text-center">
                                                                    @if($factura->estado == 'PROCESADO') <span class="text-success fw-bold" style="font-size: 0.7rem;"><i class="fas fa-check me-1"></i> PROCESADO</span>
                                                                    @elseif($factura->anular == 1) <span class="text-danger fw-bold" style="font-size: 0.7rem;"><i class="fas fa-ban me-1"></i> ANULADO</span>
                                                                    @else <span class="text-secondary fw-semibold" style="font-size: 0.7rem;"><i class="fas fa-hourglass-half me-1"></i> PENDIENTE</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                @endforeach

                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-white rounded-4 border">
                                        <i class="fas fa-database fs-1 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-bold text-dark">Sin Datos en el ERP</h6>
                                        <p class="mb-0 fs-7">No se encontraron facturas asociadas a este cliente en el bloque.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 2: ALERTAS --}}
                            <div class="tab-pane fade" id="alertas" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-clock me-2"></i> Registro de Alertas</h6>
                                    <button class="btn btn-sm btn-outline-info rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalAlerta"><i class="fas fa-plus me-1"></i> Nueva Alerta</button>
                                </div>
                                @if($historialAlertas->count() > 0)
                                    <div class="row g-3">
                                        @foreach($historialAlertas as $alerta)
                                            @php $esAlertaBloque = is_null($alerta->id_car_sia_operaciones); @endphp
                                            <div class="col-12">
                                                <div class="p-3 border rounded-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center bg-white shadow-sm hover-opacity">
                                                    <div class="d-flex align-items-center gap-3 mb-2 mb-sm-0">
                                                        <div class="bg-pastel-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;"><i class="fas fa-bell text-info fs-5"></i></div>
                                                        <div>
                                                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                                                {{ $alerta->tipoAlerta->nombre ?? 'Tipo de Alerta Desconocido' }}
                                                                @if($esAlertaBloque) <span class="badge bg-pastel-primary px-2 py-1" style="font-size: 0.65rem;"><i class="fas fa-layer-group"></i> Lote</span>
                                                                @else <span class="badge bg-pastel-secondary text-dark px-2 py-1 border" style="font-size: 0.65rem;"><i class="fas fa-user"></i> Cliente</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-muted fs-8 mt-1 d-flex align-items-center flex-wrap gap-3">
                                                                <span><i class="far fa-calendar-alt me-1"></i> Programada para: <span class="fw-semibold text-dark">{{ $alerta->fecha_programada ? \Carbon\Carbon::parse($alerta->fecha_programada)->format('d/m/Y') : 'N/A' }}</span></span>
                                                                <span><i class="fas fa-user-edit text-muted opacity-50 me-1"></i> Creada por: <span class="fw-semibold text-dark">{{ optional($alerta->usuario)->name ?? 'Sistema' }}</span></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm-end ms-5 ms-sm-0">
                                                        @if($alerta->procesado_en)
                                                            <span class="badge bg-pastel-success rounded-pill px-3 py-2 mb-1"><i class="fas fa-check me-1"></i> Procesada</span>
                                                            <div class="text-muted" style="font-size: 0.7rem;">El {{ \Carbon\Carbon::parse($alerta->procesado_en)->format('d/m/Y h:i A') }}</div>
                                                        @else
                                                            <span class="badge bg-pastel-warning rounded-pill px-3 py-2"><i class="fas fa-hourglass-half me-1"></i> Pendiente</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed">
                                        <i class="fas fa-bell-slash fs-1 text-secondary mb-3 opacity-25"></i>
                                        <p class="mb-0 fw-semibold">No hay alertas programadas para esta operación.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 3: HISTORIAL ETL --}}
                            <div class="tab-pane fade" id="historial" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted mb-4 fs-8 text-uppercase"><i class="fas fa-exchange-alt me-2"></i> Transiciones de Estado</h6>
                                        @if($historialEstados->count() > 0)
                                            <div class="border-start border-2 border-primary border-opacity-25 ms-3 ps-4 position-relative">
                                                @foreach($historialEstados as $historialEstado)
                                                    @php $esEstadoBloque = is_null($historialEstado->id_car_sia_operaciones); @endphp
                                                    <div class="mb-4 position-relative">
                                                        <span class="position-absolute bg-white border border-2 border-primary rounded-circle" style="width: 14px; height: 14px; left: -1.8rem; top: 0.25rem;"></span>
                                                        <div class="fw-bold text-dark d-flex align-items-center gap-2 flex-wrap">
                                                            {{ $historialEstado->estado->nombre ?? 'Estado Desconocido' }}
                                                            @if($esEstadoBloque) <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> API-{{ str_pad($historialEstado->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                            @else <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-user me-1"></i> Individual</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted fs-8 mt-1 d-flex flex-wrap gap-2">
                                                            <span><i class="far fa-clock me-1"></i> {{ $historialEstado->created_at ? $historialEstado->created_at->format('d M, Y h:i A') : 'Fecha no disponible' }}</span>
                                                            <span>| <i class="fas fa-user-tag text-muted opacity-50 ms-1 me-1"></i> {{ optional($historialEstado->usuario)->name ?? 'Sistema' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bg-light p-3 rounded-4 text-center text-muted fs-8">Sin historial de estados registrado.</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted mb-4 fs-8 text-uppercase"><i class="fas fa-tags me-2"></i> Eventos Inyectados</h6>
                                        @if($historialTipos->count() > 0)
                                            <div class="border-start border-2 border-info border-opacity-25 ms-3 ps-4 position-relative">
                                                @foreach($historialTipos as $historialTipo)
                                                    <div class="mb-4 position-relative">
                                                        <span class="position-absolute bg-white border border-2 border-info rounded-circle" style="width: 14px; height: 14px; left: -1.8rem; top: 0.25rem;"></span>
                                                        <div class="fw-bold text-dark d-flex align-items-center gap-2 flex-wrap">
                                                            {{ $historialTipo->tipo->nombre ?? 'Tipo Desconocido' }}
                                                            @if($historialTipo->es_lote) <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> API-{{ str_pad($historialTipo->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                            @else <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-user me-1"></i> Individual</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted fs-8 mt-1 d-flex flex-wrap gap-2">
                                                            <span><i class="far fa-clock me-1"></i> {{ $historialTipo->created_at ? $historialTipo->created_at->format('d M, Y h:i A') : 'Fecha no disponible' }}</span>
                                                            <!-- SE APLICA VARIABLES DEL CONTROLADOR EN EL HISTORIAL TAMBIÉN -->
                                                            <span>| <i class="fas fa-user-edit text-muted opacity-50 ms-1 me-1"></i> {{ $historialTipo->nombre_user ?? 'Sistema' }}{{ $historialTipo->cargo_user ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bg-light p-3 rounded-4 text-center text-muted fs-8">Sin eventos inyectados.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 4: CERTIFICADOS (Estilo Hoja de Cálculo Compacta) --}}
                            <div class="tab-pane fade" id="certificados" role="tabpanel">
                                @if($operacion->lineas && $operacion->lineas->count() > 0)

                                    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.75rem;">
                                                @foreach($historialTipos as $registro)
                                                    @php
                                                        $certId = $loop->iteration;
                                                        $tipo = $registro->tipo;
                                                        $versionesDeEsteTipo = $registro->versionesDeEsteTipo;
                                                        $hashActual = $registro->hashActual;
                                                        $lineasEditor = $registro->lineasEditor;
                                                    @endphp

                                                    {{-- Fila Agrupadora (Botón Colapsable del Certificado) --}}
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="p-0 border-bottom-0">
                                                                <button class="btn btn-light w-100 d-flex justify-content-between align-items-center rounded-0 px-3 py-2 shadow-none border-0 text-start collapsed"
                                                                        type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapseCertificado-{{ $certId }}"
                                                                        aria-expanded="false"
                                                                        aria-controls="collapseCertificado-{{ $certId }}"
                                                                        style="background-color: #f8f9fa;">

                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <i class="fas fa-chevron-down text-secondary" style="font-size: 0.7rem;"></i>
                                                                        <i class="fas fa-file-pdf text-danger" style="font-size: 1rem;"></i>
                                                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $tipo->nombre ?? 'Documento ' . $certId }}</span>

                                                                        @if($registro->es_lote)
                                                                            <span class="badge bg-pastel-primary text-primary px-2 py-1 rounded-1" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> Lote API-{{ str_pad($registro->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                                        @else
                                                                            <span class="badge bg-pastel-secondary text-dark px-2 py-1 rounded-1" style="font-size: 0.65rem;"><i class="fas fa-user me-1"></i> Individual</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="d-flex align-items-center gap-3 fw-normal text-muted" style="font-size: 0.7rem;">
                                                                        <span><i class="far fa-clock me-1"></i> {{ $registro->created_at ? $registro->created_at->format('d/m/Y h:i A') : 'N/A' }}</span>
                                                                        <span class="border-start border-secondary ps-3">
                                                                            <i class="fas fa-user-circle me-1 opacity-75"></i>
                                                                            <strong class="text-dark">{{ $registro->nombre_user ?? 'Sistema' }}</strong>
                                                                            <span class="fst-italic opacity-75">{{ $registro->cargo_user ?? '' }}</span>
                                                                        </span>
                                                                    </div>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    {{-- Cuerpo del Certificado (Controles, PDF y Editor) --}}
                                                    <tbody id="collapseCertificado-{{ $certId }}" class="collapse border-bottom" style="border-bottom-width: 2px !important; border-color: var(--c-border) !important;">
                                                        <tr>
                                                            <td class="p-3 bg-white">

                                                                {{-- Controles --}}
                                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 pb-3 border-bottom border-dashed">
                                                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase mb-2 mb-md-0"><i class="fas fa-sliders-h me-2"></i> Controles del Documento</h6>

                                                                    <div class="d-flex align-items-center gap-3">
                                                                        @if($versionesDeEsteTipo->count() > 1)
                                                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-1 px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalVersiones_{{ $certId }}">
                                                                                <i class="fas fa-history me-1"></i> Versiones ({{ $versionesDeEsteTipo->count() }})
                                                                            </button>
                                                                        @endif

                                                                        <div class="btn-group shadow-sm bg-light border rounded-1 p-1">
                                                                            <button type="button" class="btn btn-sm btn-danger px-3 active fw-bold border-0" style="border-radius: 4px;" id="btnModePdf_{{ $certId }}" onclick="toggleMode('pdf', '{{ $certId }}')">
                                                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-light text-success px-3 fw-bold border-0" style="border-radius: 4px;" id="btnModeData_{{ $certId }}" onclick="toggleMode('data', '{{ $certId }}')">
                                                                                <i class="fas fa-table me-1"></i> Editor de Datos
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- VISOR PDF --}}
                                                                <div id="pdfViewerContainer_{{ $certId }}" class="border rounded-2 overflow-hidden shadow-sm bg-light" style="height: 650px;">
                                                                    <iframe src="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $tipo->id ?? null, 'hash' => $hashActual]) }}" width="100%" height="100%" frameborder="0" style="background-color: #f8fafc;"></iframe>
                                                                </div>

                                                                {{-- EDITOR DE DATOS (Estilo Excel Integrado Avanzado) --}}
                                                                <div id="dataEditorContainer_{{ $certId }}" class="d-none">
                                                                    <form id="formEditor_{{ $certId }}" action="{{ route('certificados.operaciones.actualizar_lineas', $operacion->id) }}" method="POST">
                                                                        @csrf @method('PUT')
                                                                        <input type="hidden" name="tipo_certificado_id" value="{{ $tipo->id ?? '' }}">

                                                                        <div class="alert bg-pastel-primary border-0 rounded-2 py-2 px-3 mb-2 d-flex align-items-center" style="font-size: 0.75rem;">
                                                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                                                            <span class="text-muted">Las columnas resaltadas con el ícono <i class="fas fa-pen text-primary mx-1"></i> son editables. Haz clic sobre ellas para modificar su valor.</span>
                                                                        </div>

                                                                        <div class="table-responsive border rounded-2 shadow-sm mb-3">
                                                                            <table class="table table-sm table-bordered table-hover align-middle mb-0" style="font-size: 0.75rem; min-width: 1500px;">
                                                                                <thead class="text-muted text-uppercase" style="font-size: 0.65rem; background-color: #f1f5f9;">
                                                                                    <tr>
                                                                                        {{-- SECCIÓN INFO ERP (Read-Only) --}}
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 5%;">Lote</th>
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 7%;">Factura</th>
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 4%;">Cuota</th>
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 7%;">Pagaré</th>
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-end" style="width: 7%;">Valor $</th>
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-center" style="width: 7%;">Estado ERP</th>
                                                                                        <th class="px-2 py-2 border-bottom text-secondary text-center border-end" style="width: 7%;">Cuenta</th>

                                                                                        {{-- SECCIÓN EDITABLE --}}
                                                                                        <th class="px-2 py-2 border-bottom text-primary text-center bg-white" style="width: 8%;"><i class="fas fa-pen me-1"></i> Calificación</th>
                                                                                        <th class="px-2 py-2 border-bottom text-primary text-center bg-white" style="width: 10%;"><i class="fas fa-pen me-1"></i> Estado Línea</th>
                                                                                        <th class="px-2 py-2 border-bottom text-primary text-center bg-white" style="width: 6%;"><i class="fas fa-pen me-1"></i> Mora</th>
                                                                                        <th class="px-2 py-2 border-bottom text-primary text-center bg-white" style="width: 8%;"><i class="fas fa-pen me-1"></i> Vencimiento</th>
                                                                                        <th class="px-2 py-2 border-bottom text-primary text-center bg-white" style="width: 8%;"><i class="fas fa-pen me-1"></i> Último Rec.</th>
                                                                                        <th class="px-2 py-2 border-bottom text-primary text-center bg-white" style="width: 8%;"><i class="fas fa-pen me-1"></i> Procesado</th>
                                                                                        <th class="px-2 py-2 border-bottom text-primary bg-white" style="width: 13%;"><i class="fas fa-pen me-1"></i> Observación</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody style="border-bottom-width: 2px !important; border-color: var(--c-border) !important;">
                                                                                    @forelse($lineasEditor as $linea)
                                                                                        <tr class="bg-white">

                                                                                            {{-- COLUMNAS INFORMATIVAS (Fondo Gris Claro) --}}
                                                                                            <td class="px-2 py-1 text-center text-muted" style="background-color: #f8fafc; font-size: 0.7rem;">API-{{ str_pad($linea->numero_bloque, 4, '0', STR_PAD_LEFT) }}</td>
                                                                                            <td class="px-2 py-1 text-center fw-bold text-dark" style="background-color: #f8fafc; font-family: monospace;">#{{ $linea->id_factura }}</td>
                                                                                            <td class="px-2 py-1 text-center text-muted fw-bold" style="background-color: #f8fafc;">{{ $linea->factura->cuota ?? '-' }}</td>
                                                                                            <td class="px-2 py-1 text-center text-muted" style="background-color: #f8fafc; font-size: 0.7rem;">{{ $linea->factura->pagare ?? 'S/N' }}</td>
                                                                                            <td class="px-2 py-1 text-end fw-bold" style="background-color: #f8fafc; color: #047857;">${{ isset($linea->factura->valor) ? number_format((float)$linea->factura->valor, 0, ',', '.') : '0' }}</td>

                                                                                            <td class="px-2 py-1 text-center" style="background-color: #f8fafc;">
                                                                                                @if(isset($linea->factura) && $linea->factura->estado == 'PROCESADO') <span class="text-success fw-bold" style="font-size: 0.65rem;"><i class="fas fa-check"></i> PROCESADO</span>
                                                                                                @elseif(isset($linea->factura) && $linea->factura->anular == 1) <span class="text-danger fw-bold" style="font-size: 0.65rem;"><i class="fas fa-ban"></i> ANULADO</span>
                                                                                                @else <span class="text-secondary fw-semibold" style="font-size: 0.65rem;"><i class="fas fa-hourglass-half"></i> PENDIENTE</span>
                                                                                                @endif
                                                                                            </td>

                                                                                            <td class="px-2 py-1 text-center text-muted border-end" style="background-color: #f8fafc;">{{ $linea->id_car_sia_lineas }}</td>

                                                                                            {{-- COLUMNAS EDITABLES (Celdas tipo Excel sin bordes internos) --}}
                                                                                            <td class="p-0 align-middle">
                                                                                                <select name="lineas[{{ $linea->id }}][calificacion]" class="form-select form-select-sm border-0 shadow-none text-center fw-bold w-100 rounded-0 bg-transparent py-1 {{ $linea->calificacion == 'Bueno' ? 'text-success' : ($linea->calificacion == 'Regular' ? 'text-warning' : 'text-danger') }}" onchange="this.className = 'form-select form-select-sm border-0 shadow-none text-center fw-bold w-100 rounded-0 bg-transparent py-1 ' + (this.value == 'Bueno' ? 'text-success' : (this.value == 'Regular' ? 'text-warning' : 'text-danger'))">
                                                                                                    <option class="text-dark" value="Bueno" {{ $linea->calificacion == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                                                                                    <option class="text-dark" value="Regular" {{ $linea->calificacion == 'Regular' ? 'selected' : '' }}>Regular</option>
                                                                                                    <option class="text-dark" value="Irregular" {{ $linea->calificacion == 'Irregular' ? 'selected' : '' }}>Irregular</option>
                                                                                                </select>
                                                                                            </td>

                                                                                            <td class="p-0 align-middle border-start border-end">
                                                                                                <select name="lineas[{{ $linea->id }}][id_car_sia_estados]" class="form-select form-select-sm border-0 shadow-none text-center text-muted fw-semibold w-100 rounded-0 bg-transparent py-1">
                                                                                                    <option value="">Seleccione...</option>
                                                                                                    @foreach($estados as $est)
                                                                                                        <option value="{{ $est->id }}" {{ $linea->id_car_sia_estados == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </td>

                                                                                            <td class="p-0 align-middle">
                                                                                                <input type="number" name="lineas[{{ $linea->id }}][dias_mora_automaticos]" class="form-control form-control-sm border-0 shadow-none text-center fw-bold w-100 rounded-0 bg-transparent py-1 {{ $linea->dias_mora_automaticos < 0 ? 'text-danger' : 'text-dark' }}" value="{{ $linea->dias_mora_automaticos }}" required>
                                                                                            </td>

                                                                                            <td class="p-0 align-middle border-start">
                                                                                                <input type="date" name="lineas[{{ $linea->id }}][fecha_venci]" class="form-control form-control-sm border-0 shadow-none text-center w-100 rounded-0 bg-transparent text-muted py-1" value="{{ $linea->fecha_venci ? \Carbon\Carbon::parse($linea->fecha_venci)->format('Y-m-d') : '' }}">
                                                                                            </td>

                                                                                            <td class="p-0 align-middle border-start">
                                                                                                <input type="date" name="lineas[{{ $linea->id }}][fecha_ultimo_recordatorio]" class="form-control form-control-sm border-0 shadow-none text-center w-100 rounded-0 bg-transparent text-muted py-1" value="{{ $linea->fecha_ultimo_recordatorio ? \Carbon\Carbon::parse($linea->fecha_ultimo_recordatorio)->format('Y-m-d') : '' }}">
                                                                                            </td>

                                                                                            <td class="p-0 align-middle border-start">
                                                                                                <input type="date" name="lineas[{{ $linea->id }}][procesado_en]" class="form-control form-control-sm border-0 shadow-none text-center w-100 rounded-0 bg-transparent text-muted py-1" value="{{ $linea->procesado_en ? \Carbon\Carbon::parse($linea->procesado_en)->format('Y-m-d') : '' }}">
                                                                                            </td>

                                                                                            <td class="p-0 align-middle border-start">
                                                                                                <input type="text" name="lineas[{{ $linea->id }}][observacion]" class="form-control form-control-sm border-0 shadow-none w-100 rounded-0 bg-transparent py-1 px-2" value="{{ $linea->observacion }}" placeholder="...">
                                                                                            </td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr><td colspan="14" class="text-center py-4 text-muted"><i class="fas fa-info-circle me-2"></i> No hay líneas procesadas para editar en esta versión.</td></tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        @if($lineasEditor->count() > 0)
                                                                            <div class="text-end">
                                                                                <button type="button" class="btn btn-success btn-sm rounded-1 px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmSave_{{ $certId }}">
                                                                                    <i class="fas fa-save me-1"></i> Guardar Nueva Versión
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    </form>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>

                                    {{-- ========================================== --}}
                                    {{-- BLOQUE DE MODALES (Fuera de la tabla principal para evitar errores de renderizado) --}}
                                    {{-- ========================================== --}}
                                    @foreach($historialTipos as $registro)
                                        @php
                                            $certId = $loop->iteration;
                                            $tipo = $registro->tipo;
                                            $versionesDeEsteTipo = $registro->versionesDeEsteTipo;
                                            $lineasEditor = $registro->lineasEditor;
                                        @endphp

                                        {{-- Modal de Historial de Versiones --}}
                                        @if($versionesDeEsteTipo->count() > 1)
                                            <div class="modal fade" id="modalVersiones_{{ $certId }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-history me-2"></i> Historial de Versiones</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <p class="text-muted fs-7 mb-4">Iteraciones guardadas para el evento <strong>{{ $tipo->nombre }}</strong>.</p>
                                                            <div class="table-responsive border rounded-3">
                                                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                                                    <thead class="bg-light text-muted text-uppercase">
                                                                        <tr>
                                                                            <th class="ps-3 py-2 border-0">Versión / Hash</th>
                                                                            <th class="py-2 border-0">Fecha de Edición</th>
                                                                            <th class="py-2 text-end pe-3 border-0">Acciones</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($versionesDeEsteTipo as $index => $version)
                                                                            <tr>
                                                                                <td class="ps-3 py-2">
                                                                                    <span class="badge bg-pastel-secondary text-dark border font-monospace text-truncate d-inline-block align-middle" style="max-width: 180px;" title="{{ $version->hash_certificado }}">{{ $version->hash_certificado }}</span>
                                                                                    @if($loop->first) <span class="badge bg-success ms-1 align-middle">Actual</span> @endif
                                                                                </td>
                                                                                <td class="py-2">
                                                                                    <div class="fw-bold text-dark">{{ $version->created_at->format('d/m/Y') }}</div>
                                                                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $version->created_at->format('h:i A') }}</div>
                                                                                </td>
                                                                                <td class="py-2 text-end pe-3">
                                                                                    <button type="button" class="btn btn-sm btn-light border rounded-1 px-2 shadow-sm me-1" data-bs-target="#modalRegistros_{{ $version->hash_certificado }}" data-bs-toggle="modal" data-bs-dismiss="modal" title="Ver Registros"><i class="fas fa-list text-secondary"></i></button>
                                                                                    <a href="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $tipo->id, 'hash' => $version->hash_certificado]) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-1 px-2 shadow-sm" title="Ver PDF Antiguo"><i class="fas fa-file-pdf"></i></a>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Modales de Detalle por Hash --}}
                                        @foreach($versionesDeEsteTipo as $version)
                                            <div class="modal fade" id="modalRegistros_{{ $version->hash_certificado }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                            <h6 class="fw-bold mb-0 text-secondary"><i class="fas fa-list-alt me-2"></i> Detalle de Registros</h6>
                                                            <button type="button" class="btn-close" data-bs-target="#modalVersiones_{{ $certId }}" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <p class="text-muted fs-8 mb-3">Hash: <span class="font-monospace text-dark">{{ $version->hash_certificado }}</span></p>
                                                            <div class="table-responsive border rounded-3">
                                                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.75rem;">
                                                                    <thead class="bg-light text-muted text-uppercase">
                                                                        <tr>
                                                                            <th class="ps-3 py-2 border-0">Factura</th>
                                                                            <th class="py-2 border-0 text-center">Calificación</th>
                                                                            <th class="py-2 border-0 text-center">Días Mora</th>
                                                                            <th class="py-2 border-0">Fecha</th>
                                                                            <th class="py-2 pe-3 border-0 text-end">Usuario</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $lineasDelHash = collect($registro->lineasParaEsteTipo)->where('hash_certificado', $version->hash_certificado);
                                                                        @endphp
                                                                        @foreach($lineasDelHash as $lineaHash)
                                                                            <tr>
                                                                                <td class="ps-3 py-2 fw-bold text-dark font-monospace">#{{ $lineaHash->id_factura }}</td>
                                                                                <td class="py-2 text-center"><span class="badge rounded-1 {{ $lineaHash->calificacion == 'Bueno' ? 'bg-success' : ($lineaHash->calificacion == 'Regular' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $lineaHash->calificacion }}</span></td>
                                                                                <td class="py-2 text-center fw-bold">{{ $lineaHash->dias_mora_automaticos }}</td>
                                                                                <td class="py-2 text-muted">{{ $lineaHash->created_at ? $lineaHash->created_at->format('d/m/y h:i A') : 'N/A' }}</td>
                                                                                <td class="py-2 pe-3 text-end text-muted"><i class="fas fa-user-circle me-1 opacity-50"></i> {{ optional($lineaHash->usuario)->name ?? 'Sistema' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                            <button type="button" class="btn btn-sm btn-light border rounded-1 px-4 shadow-sm" data-bs-target="#modalVersiones_{{ $certId }}" data-bs-toggle="modal" data-bs-dismiss="modal">
                                                                <i class="fas fa-arrow-left me-1"></i> Volver a Versiones
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Modal Guardar Edición --}}
                                        @if($lineasEditor->count() > 0)
                                            <div class="modal fade" id="modalConfirmSave_{{ $certId }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                            <h5 class="fw-bold mb-0 text-success"><i class="fas fa-code-branch me-2"></i> Generar Nueva Versión</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="alert bg-pastel-success text-dark border-0 rounded-3 mb-0" style="font-size: 0.85rem;">
                                                                <i class="fas fa-info-circle fs-5 mb-2 d-block text-success"></i>
                                                                Se generará una <strong>nueva versión del certificado</strong> con los cambios aplicados en la tabla.<br><br>
                                                                La versión antigua no se perderá y seguirá en el historial.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                            <button type="button" class="btn btn-sm btn-light rounded-1 px-3" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="button" class="btn btn-sm btn-success rounded-1 px-4 fw-bold shadow-sm" onclick="document.getElementById('formEditor_{{ $certId }}').submit(); this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin me-2\'></i> Guardando...';">
                                                                Confirmar y Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @endforeach

                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed">
                                        <i class="fas fa-file-excel fs-1 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-bold text-dark">Líneas No Estructuradas</h6>
                                        <p class="mb-0 fs-7">El certificado no cuenta con datos procesados aún. Utiliza el botón <strong class="text-danger">Generar Certificado</strong>.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- ======================================================================= -->
                            <!-- TAB 5: GRÁFICOS E INDICADORES                                           -->
                            <!-- ======================================================================= -->
                            <div class="tab-pane fade" id="graficos" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-chart-line me-2"></i> Resumen Visual de la Operación</h6>
                                </div>

                                @php
                                    // 1. Calcular datos para la gráfica de Cartera (Estado de las facturas)
                                    $chartCarteraData = ['Procesado' => 0, 'Pendiente' => 0, 'Anulado' => 0];
                                    foreach($lineasAgrupadas as $grupo) {
                                        foreach($grupo['facturas'] as $factura) {
                                            if ($factura->estado == 'PROCESADO') {
                                                $chartCarteraData['Procesado']++;
                                            } elseif ($factura->anular == 1) {
                                                $chartCarteraData['Anulado']++;
                                            } else {
                                                $chartCarteraData['Pendiente']++;
                                            }
                                        }
                                    }

                                    // 2. Calcular datos para la gráfica de Eventos (Auditoría)
                                    $chartEventosData = $logsAuditoria->groupBy('tituloEvento')->map->count();
                                @endphp

                                <div class="row g-4">
                                    <!-- Gráfico 1: Estado de las Facturas -->
                                    <div class="col-md-6">
                                        <div class="border rounded-4 p-4 bg-white shadow-sm h-100">
                                            <h6 class="fw-bold text-dark fs-7 mb-3 text-center">Composición de Cartera</h6>
                                            <div class="d-flex justify-content-center align-items-center bg-light rounded-3" style="height: 250px; position: relative;">
                                                <canvas id="chartCartera"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gráfico 2: Tiempos o Tipos de Eventos -->
                                    <div class="col-md-6">
                                        <div class="border rounded-4 p-4 bg-white shadow-sm h-100">
                                            <h6 class="fw-bold text-dark fs-7 mb-3 text-center">Distribución de Eventos (ETL)</h6>
                                            <div class="d-flex justify-content-center align-items-center bg-light rounded-3" style="height: 250px; position: relative;">
                                                <canvas id="chartEventos"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ======================================================================= -->
                            <!-- TAB 6: OPERARIOS Y RENDIMIENTO                                          -->
                            <!-- ======================================================================= -->
                            <div class="tab-pane fade" id="operarios" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-users me-2"></i> Índice de Intervención por Usuario</h6>
                                </div>

                                @if($operariosData->count() > 0)
                                    <div class="row g-3">
                                        @foreach($operariosData as $operario)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="p-3 border rounded-4 bg-white shadow-sm hover-opacity transition-all h-100">

                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="bg-pastel-primary rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                                                            <i class="fas fa-user-astronaut text-primary fs-5"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="fw-bold text-dark mb-0 fs-7 text-truncate" style="max-width: 180px;" title="{{ $operario['nombre'] }}">{{ $operario['nombre'] }}</h6>
                                                            <span class="text-muted" style="font-size: 0.7rem;">{{ $operario['cargo'] }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Barra de progreso del índice de intervención -->
                                                    <div class="mb-2 d-flex justify-content-between align-items-end">
                                                        <span class="text-muted fw-semibold" style="font-size: 0.75rem;">Procesos ejecutados</span>
                                                        <span class="fw-bold text-primary fs-6">{{ $operario['cantidad'] }}</span>
                                                    </div>

                                                    <div class="progress mb-3" style="height: 6px; background-color: #f1f5f9; border-radius: 4px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $operario['porcentaje'] }}%; border-radius: 4px;" aria-valuenow="{{ $operario['porcentaje'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                        <span class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-history me-1"></i> Último: {{ $operario['ultimo'] }}</span>
                                                        <span class="badge bg-pastel-success text-success" style="font-size: 0.7rem;">{{ $operario['porcentaje'] }}% del total</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed">
                                        <i class="fas fa-user-slash fs-1 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-bold text-dark">Sin Intervenciones</h6>
                                        <p class="mb-0 fs-7">Aún no hay registros de operarios en esta operación.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALES GLOBALES --}}
    <div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formGenerarCertificado" action="{{ route('certificados.operaciones.asignar_tipo', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="fas fa-file-pdf text-danger me-2"></i> Generar Certificado</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3 mt-2">
                        <label for="id_car_sia_tipos" class="form-label fw-semibold text-muted">Seleccionar evento</label>
                        <select name="id_car_sia_tipos" id="id_car_sia_tipos" class="form-select form-select-lg bg-light border-0" required>
                            <option value="">Seleccione una opción...</option>
                            @isset($tipos) @foreach($tipos as $tipo) <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option> @endforeach @endisset
                        </select>
                    </div>
                    <div id="loadingCertificado" class="d-none mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted fw-semibold" style="font-size: 0.75rem;"><i class="fas fa-cogs me-1"></i> Ensamblando PDF y Hash...</span>
                            <span class="text-danger fw-bold" style="font-size: 0.75rem;">Por favor espera</span>
                        </div>
                        <div class="progress-minimalist"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" id="btnCancelCertificado" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSubmitCertificado" class="btn btn-danger rounded-pill px-4 fw-bold text-white shadow-sm">Generar y Asignar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalTransicionar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.operaciones.transicionar', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="fas fa-exchange-alt text-warning me-2"></i> Cambiar Estado</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3">
                        <select name="id_car_sia_estados" id="id_car_sia_estados" class="form-select bg-light border-0" required>
                            <option value="">Seleccione un estado</option>
                            @isset($estados) @foreach($estados as $estado) <option value="{{ $estado->id }}">{{ $estado->nombre }}</option> @endforeach @endisset
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Guardar cambio</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalAlerta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.operaciones.programar_alerta', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4"><h5 class="fw-bold mb-0"><i class="fas fa-bell text-info me-2"></i> Programar Alerta</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body p-4">
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3">
                        <select name="id_car_sia_tipos_alerta" id="id_car_sia_tipos_alerta" class="form-select bg-light border-0" required>
                            <option value="">Seleccione una alerta</option>
                            @isset($tiposAlerta) @foreach($tiposAlerta as $tipoAlerta) <option value="{{ $tipoAlerta->id }}">{{ $tipoAlerta->nombre }}</option> @endforeach @endisset
                        </select>
                    </div>
                    <div class="mb-0"><input type="date" name="fecha_programada" class="form-control bg-light border-0" required></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold text-white">Programar Alerta</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMode(mode, certId) {
            const btnPdf = document.getElementById('btnModePdf_' + certId);
            const btnData = document.getElementById('btnModeData_' + certId);
            const containerPdf = document.getElementById('pdfViewerContainer_' + certId);
            const containerData = document.getElementById('dataEditorContainer_' + certId);

            if (mode === 'pdf') {
                btnPdf.classList.replace('btn-light', 'btn-danger'); btnPdf.classList.replace('text-danger', 'text-white');
                btnData.classList.replace('btn-success', 'btn-light'); btnData.classList.replace('text-white', 'text-success');
                containerPdf.classList.remove('d-none'); containerData.classList.add('d-none');
            } else {
                btnData.classList.replace('btn-light', 'btn-success'); btnData.classList.replace('text-success', 'text-white');
                btnPdf.classList.replace('btn-danger', 'btn-light'); btnPdf.classList.replace('text-white', 'text-danger');
                containerData.classList.remove('d-none'); containerPdf.classList.add('d-none');
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            const formCertificado = document.getElementById('formGenerarCertificado');
            const btnSubmit = document.getElementById('btnSubmitCertificado');
            if (formCertificado) {
                formCertificado.addEventListener('submit', function () {
                    btnSubmit.disabled = true; btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Procesando...';
                    document.getElementById('btnCancelCertificado').classList.add('d-none');
                    document.getElementById('loadingCertificado').classList.remove('d-none');
                });
            }
        });
    </script>

    <!-- LIBRERÍA CHART.JS (Asegúrate de no tenerla duplicada si tu layout base ya la incluye) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- GRÁFICO 1: COMPOSICIÓN DE CARTERA (Doughnut) ---
            const ctxCartera = document.getElementById('chartCartera');
            if (ctxCartera) {
                new Chart(ctxCartera.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Procesado', 'Pendiente', 'Anulado'],
                        datasets: [{
                            data: [
                                {{ $chartCarteraData['Procesado'] }},
                                {{ $chartCarteraData['Pendiente'] }},
                                {{ $chartCarteraData['Anulado'] }}
                            ],
                            backgroundColor: ['#10b981', '#64748b', '#ef4444'], // Verde, Gris, Rojo
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        cutout: '70%'
                    }
                });
            }

            // --- GRÁFICO 2: DISTRIBUCIÓN DE EVENTOS (Barras) ---
            const ctxEventos = document.getElementById('chartEventos');
            if (ctxEventos) {
                const eventosLabels = {!! json_encode($chartEventosData->keys()) !!};
                const eventosData = {!! json_encode($chartEventosData->values()) !!};

                new Chart(ctxEventos.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: eventosLabels,
                        datasets: [{
                            label: 'Cantidad de Eventos',
                            data: eventosData,
                            backgroundColor: '#3b82f6', // Azul
                            borderRadius: 6,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 } // Para que muestre números enteros (0, 1, 2...)
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-base-layout>
