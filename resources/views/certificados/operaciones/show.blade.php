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
                            
                            {{-- TAB 1: LÍNEAS --}}
                            <div class="tab-pane fade show active" id="lineas" role="tabpanel">
                                @if($lineasAgrupadas->count() > 0)
                                    <div class="accordion accordion-custom" id="accordionLineas">
                                        @foreach($lineasAgrupadas as $nombreLinea => $datosLinea)
                                            <div class="accordion-item border-0 mb-3 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                                                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} bg-white border-0 shadow-none px-4 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}">
                                                        <div class="d-flex align-items-center w-100 me-3">
                                                            <div class="bg-pastel-primary rounded-circle d-flex justify-content-center align-items-center me-3 flex-shrink-0" style="width: 48px; height: 48px;"><i class="fas fa-box-open text-primary fs-5"></i></div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold text-dark mb-1 fs-5">{{ $nombreLinea }}</h6>
                                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                                    <span class="badge bg-pastel-info text-dark px-2 py-1"><i class="fas fa-file-invoice me-1 opacity-50"></i> {{ $datosLinea['count'] }} Facturas</span>
                                                                    <span class="badge bg-pastel-success text-dark px-2 py-1"><i class="fas fa-dollar-sign me-1 opacity-50"></i> Total: ${{ number_format((float)$datosLinea['total'], 2) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>
                                                <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionLineas">
                                                    <div class="accordion-body p-0 border-top">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover table-inner align-middle mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="ps-4 py-3 border-0">N° Factura</th>
                                                                        <th class="py-3 border-0 text-center">Cuota</th>
                                                                        <th class="py-3 border-0">Pagaré</th>
                                                                        <th class="py-3 border-0">Vencimiento (ERP)</th>
                                                                        <th class="py-3 border-0 text-end">Valor a Pagar</th>
                                                                        <th class="py-3 border-0 text-center pe-4">Estado (ETL)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($datosLinea['facturas'] as $factura)
                                                                        <tr>
                                                                            <td class="ps-4 fw-bold text-dark border-bottom py-3" style="font-family: monospace;">#{{ $factura->id_factura ?? 'N/A' }}</td>
                                                                            <td class="text-center border-bottom py-3"><span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold cuota-badge">{{ $factura->cuota ?? '-' }}</span></td>
                                                                            <td class="border-bottom py-3"><div class="fw-semibold text-dark">{{ $factura->pagare ?? 'Sin Pagaré' }}</div></td>
                                                                            <td class="border-bottom py-3">
                                                                                @if($factura->fecha_venci)
                                                                                    <div class="text-dark fw-semibold"><i class="far fa-calendar-alt text-muted me-1"></i> {{ $factura->fechaVFormateada }}</div>
                                                                                    @if($factura->diasMoraCalculados < 0) <div class="text-danger fs-8 fw-bold mt-1">{{ abs(intval($factura->diasMoraCalculados)) }} días vencidos</div> @endif
                                                                                @else
                                                                                    <span class="text-muted">No Definida</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end border-bottom py-3">
                                                                                <div class="fw-bold fs-6" style="color: #047857;">${{ number_format((float)$factura->valor, 2) }}</div>
                                                                                <div class="text-muted fs-8">Bruto: ${{ number_format((float)$factura->valor_inicial, 2) }}</div>
                                                                            </td>
                                                                            <td class="text-center pe-4 border-bottom py-3">
                                                                                @if($factura->estado == 'PROCESADO') <span class="badge bg-pastel-success text-success border border-success border-opacity-25 rounded-pill px-3 py-1"><i class="fas fa-check-circle me-1"></i> {{ $factura->estado }}</span>
                                                                                @elseif($factura->anular == 1) <span class="badge bg-pastel-warning text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1"><i class="fas fa-ban me-1"></i> ANULADO</span>
                                                                                @else <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25 rounded-pill px-3 py-1"><i class="fas fa-hourglass-half me-1"></i> PENDIENTE</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed">
                                        <i class="fas fa-database fs-1 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-bold text-dark">Sin Datos en el ERP</h6>
                                        <p class="mb-0 fs-7">No se encontraron facturas asociadas.</p>
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

                            {{-- TAB 4: CERTIFICADOS --}}
                            <div class="tab-pane fade" id="certificados" role="tabpanel">
                                <div class="accordion accordion-custom" id="accordionCertificados">
                                    @if($operacion->lineas && $operacion->lineas->count() > 0)

                                        @foreach($historialTipos as $registro)
                                            @php
                                                $certId = $loop->iteration;
                                                $tipo = $registro->tipo;

                                                // LAS VARIABLES ESTÁN PRE-CALCULADAS EN EL CONTROLADOR
                                                $versionesDeEsteTipo = $registro->versionesDeEsteTipo;
                                                $hashActual = $registro->hashActual;
                                                $lineasEditor = $registro->lineasEditor;
                                            @endphp

                                            <div class="accordion-item border-0 mb-3 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                                                <h2 class="accordion-header" id="headingCertificado{{ $certId }}">
                                                    <button class="accordion-button collapsed bg-white border-0 shadow-none px-4 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCertificado{{ $certId }}">
                                                        <div class="d-flex align-items-center w-100 me-3">
                                                            <div class="bg-pastel-danger rounded-circle d-flex justify-content-center align-items-center me-3 flex-shrink-0" style="width: 48px; height: 48px;"><i class="fas fa-file-pdf text-danger fs-5"></i></div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold text-dark mb-1 fs-5 d-flex align-items-center flex-wrap gap-2">
                                                                    {{ $tipo->nombre ?? 'Documento ' . $certId }}
                                                                    @if($registro->es_lote) <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.7rem;"><i class="fas fa-layer-group me-1"></i> Generado en Lote (API-{{ str_pad($registro->numero_bloque, 4, '0', STR_PAD_LEFT) }})</span>
                                                                    @else <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25" style="font-size: 0.7rem;"><i class="fas fa-user me-1"></i> Generado Individualmente</span>
                                                                    @endif
                                                                </h6>

                                                                <!-- IMPLEMENTACIÓN DE LAS VARIABLES USUARIO / CARGO -->
                                                                <div class="d-flex align-items-center flex-wrap gap-3 text-muted mt-2" style="font-size: 0.8rem;">
                                                                    <span><i class="far fa-clock me-1"></i> Última actualización: {{ $registro->created_at ? $registro->created_at->format('d/m/Y h:i A') : 'Desconocida' }}</span>
                                                                    <span class="border-start border-secondary ps-3">
                                                                        <i class="fas fa-user-circle me-1 opacity-75"></i>
                                                                        <strong class="text-dark">{{ $registro->nombre_user ?? 'Sistema' }}</strong><span class="fst-italic opacity-75">{{ $registro->cargo_user ?? '' }}</span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </h2>

                                                <div id="collapseCertificado{{ $certId }}" class="accordion-collapse collapse" data-bs-parent="#accordionCertificados">
                                                    <div class="accordion-body p-4 border-top">
                                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                                            <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-sliders-h me-2"></i> Controles del Documento</h6>

                                                            <div class="d-flex align-items-center gap-3">
                                                                @if($versionesDeEsteTipo->count() > 1)
                                                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVersiones_{{ $certId }}">
                                                                        <i class="fas fa-history me-1"></i> Versiones ({{ $versionesDeEsteTipo->count() }})
                                                                    </button>
                                                                @endif

                                                                <div class="btn-group shadow-sm bg-white rounded-pill p-1" role="group">
                                                                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 active fw-bold" id="btnModePdf_{{ $certId }}" onclick="toggleMode('pdf', '{{ $certId }}')">
                                                                        <i class="fas fa-file-pdf me-1"></i> Visor PDF
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-light text-success rounded-pill px-3 fw-bold" id="btnModeData_{{ $certId }}" onclick="toggleMode('data', '{{ $certId }}')">
                                                                        <i class="fas fa-table me-1"></i> Modo Hoja de Cálculo
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- VISOR PDF DINÁMICO --}}
                                                        <div id="pdfViewerContainer_{{ $certId }}" class="border-0 rounded-4 overflow-hidden shadow-sm bg-light transition-all" style="height: 650px; position: relative;">
                                                            <iframe src="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $tipo->id ?? null, 'hash' => $hashActual]) }}" width="100%" height="100%" frameborder="0" style="border: none; background-color: #f8fafc;"></iframe>
                                                        </div>

                                                        {{-- HOJA DE CÁLCULO DINÁMICA --}}
                                                        <div id="dataEditorContainer_{{ $certId }}" class="d-none transition-all mt-2">
                                                            <form id="formEditor_{{ $certId }}" action="{{ route('certificados.operaciones.actualizar_lineas', $operacion->id) }}" method="POST">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="tipo_certificado_id" value="{{ $tipo->id ?? '' }}">
                                                                <div class="table-responsive rounded-4 shadow-sm border mb-3">
                                                                    <table class="table table-spreadsheet w-100 mb-0 bg-white">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center">Factura</th>
                                                                                <th class="text-center">Cuenta</th>
                                                                                <th>Calificación</th>
                                                                                <th class="text-center">Días Mora</th>
                                                                                <th>Vencimiento</th>
                                                                                <th>Observación</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @forelse($lineasEditor as $linea)
                                                                                <tr>
                                                                                    <td class="p-2 text-center text-muted fw-bold" style="font-family: monospace; font-size: 0.85rem;">#{{ $linea->id_factura }}</td>
                                                                                    <td class="p-2 text-center text-muted" style="font-size: 0.85rem;">{{ $linea->id_car_sia_lineas }}</td>
                                                                                    <td>
                                                                                        <select name="lineas[{{ $linea->id }}][calificacion]" class="input-spreadsheet fw-semibold {{ $linea->calificacion == 'Bueno' ? 'text-success' : ($linea->calificacion == 'Regular' ? 'text-warning' : 'text-danger') }}" onchange="this.className = 'input-spreadsheet fw-semibold ' + (this.value == 'Bueno' ? 'text-success' : (this.value == 'Regular' ? 'text-warning' : 'text-danger'))">
                                                                                            <option value="Bueno" {{ $linea->calificacion == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                                                                            <option value="Regular" {{ $linea->calificacion == 'Regular' ? 'selected' : '' }}>Regular</option>
                                                                                            <option value="Irregular" {{ $linea->calificacion == 'Irregular' ? 'selected' : '' }}>Irregular</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td><input type="number" name="lineas[{{ $linea->id }}][dias_mora_automaticos]" class="input-spreadsheet text-center fw-bold" value="{{ $linea->dias_mora_automaticos }}" required></td>
                                                                                    <td><input type="date" name="lineas[{{ $linea->id }}][fecha_venci]" class="input-spreadsheet text-muted" value="{{ $linea->fecha_venci ? \Carbon\Carbon::parse($linea->fecha_venci)->format('Y-m-d') : '' }}"></td>
                                                                                    <td><input type="text" name="lineas[{{ $linea->id }}][observacion]" class="input-spreadsheet" value="{{ $linea->observacion }}"></td>
                                                                                </tr>
                                                                            @empty
                                                                                <tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-info-circle me-2"></i> No hay líneas procesadas.</td></tr>
                                                                            @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                @if($lineasEditor->count() > 0)
                                                                    <div class="text-end mt-3">
                                                                        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmSave_{{ $certId }}"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
                                                                    </div>
                                                                @endif
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- MODAL PRINCIPAL: HISTORIAL DE VERSIONES --}}
                                            @if($versionesDeEsteTipo->count() > 1)
                                                <div class="modal fade" id="modalVersiones_{{ $certId }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-history me-2"></i> Historial de Versiones</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <p class="text-muted fs-7 mb-4">Iteraciones guardadas para el evento <strong>{{ $tipo->nombre }}</strong>, separadas por Hash de seguridad.</p>
                                                                <div class="table-responsive border rounded-3">
                                                                    <table class="table table-hover align-middle mb-0">
                                                                        <thead class="bg-light text-muted small text-uppercase">
                                                                            <tr>
                                                                                <th class="ps-3 border-0">Versión / Hash</th>
                                                                                <th class="border-0">Fecha de Edición</th>
                                                                                <th class="text-end pe-3 border-0">Acciones</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($versionesDeEsteTipo as $index => $version)
                                                                                <tr>
                                                                                    <td class="ps-3 py-3">
                                                                                        <span class="badge bg-pastel-secondary text-dark border font-monospace text-truncate d-inline-block" style="max-width: 220px;" title="{{ $version->hash_certificado }}">
                                                                                            {{ $version->hash_certificado }}
                                                                                        </span>
                                                                                        @if($loop->first) <span class="badge bg-success ms-2">Actual</span> @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="fw-semibold text-dark fs-7">{{ $version->created_at->format('d/m/Y') }}</div>
                                                                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $version->created_at->format('h:i A') }}</div>
                                                                                    </td>
                                                                                    <td class="text-end pe-3">
                                                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm me-2" data-bs-target="#modalRegistros_{{ $version->hash_certificado }}" data-bs-toggle="modal" data-bs-dismiss="modal">
                                                                                            <i class="fas fa-list me-1"></i> Ver Registros
                                                                                        </button>

                                                                                        <a href="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $tipo->id, 'hash' => $version->hash_certificado]) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                                                                            <i class="fas fa-file-pdf me-1"></i> Ver PDF Antiguo
                                                                                        </a>
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

                                            {{-- MODALES SECUNDARIOS: DETALLE DE REGISTROS POR HASH --}}
                                            @foreach($versionesDeEsteTipo as $version)
                                                <div class="modal fade" id="modalRegistros_{{ $version->hash_certificado }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                                <h6 class="fw-bold mb-0 text-secondary">
                                                                    <i class="fas fa-list-alt me-2"></i> Detalle de Registros
                                                                </h6>
                                                                <button type="button" class="btn-close" data-bs-target="#modalVersiones_{{ $certId }}" data-bs-toggle="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <p class="text-muted fs-8 mb-3">Líneas asociadas al Hash: <span class="font-monospace text-dark">{{ $version->hash_certificado }}</span></p>
                                                                <div class="table-responsive border rounded-3">
                                                                    <table class="table table-sm table-hover align-middle mb-0 fs-8">
                                                                        <thead class="bg-light text-muted text-uppercase">
                                                                            <tr>
                                                                                <th class="ps-3 py-2 border-0">Factura</th>
                                                                                <th class="py-2 border-0">Calificación</th>
                                                                                <th class="py-2 border-0">Días Mora</th>
                                                                                <th class="py-2 border-0">Fecha / Hora</th>
                                                                                <th class="py-2 pe-3 border-0">Usuario</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                // Esta pequeña asignación es segura mantenerla aquí, pues ya filtramos en el controlador
                                                                                $lineasDelHash = collect($registro->lineasParaEsteTipo)->where('hash_certificado', $version->hash_certificado);
                                                                            @endphp
                                                                            @foreach($lineasDelHash as $lineaHash)
                                                                                <tr>
                                                                                    <td class="ps-3 fw-bold text-dark">#{{ $lineaHash->id_factura }}</td>
                                                                                    <td>
                                                                                        <span class="badge {{ $lineaHash->calificacion == 'Bueno' ? 'bg-success' : ($lineaHash->calificacion == 'Regular' ? 'bg-warning' : 'bg-danger') }}">
                                                                                            {{ $lineaHash->calificacion }}
                                                                                        </span>
                                                                                    </td>
                                                                                    <td class="fw-semibold">{{ $lineaHash->dias_mora_automaticos }}</td>
                                                                                    <td class="text-muted">{{ $lineaHash->created_at ? $lineaHash->created_at->format('d/m/Y h:i A') : 'N/A' }}</td>
                                                                                    <td class="pe-3 text-muted"><i class="fas fa-user-circle me-1 opacity-50"></i> {{ optional($lineaHash->usuario)->name ?? 'Sistema' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                                <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm" data-bs-target="#modalVersiones_{{ $certId }}" data-bs-toggle="modal" data-bs-dismiss="modal">
                                                                    <i class="fas fa-arrow-left me-1"></i> Volver a Versiones
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- MODAL DE CONFIRMACIÓN EDICIÓN HOJA CALCULO --}}
                                            @if($lineasEditor->count() > 0)
                                                <div class="modal fade" id="modalConfirmSave_{{ $certId }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                                <h5 class="fw-bold mb-0 text-success"><i class="fas fa-code-branch me-2"></i> Generar Nueva Versión</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="alert bg-pastel-success text-dark border-0 rounded-4 mb-0" style="font-size: 0.9rem;">
                                                                    <i class="fas fa-info-circle fs-5 mb-2 d-block text-success"></i>
                                                                    Este proceso generará una <strong>nueva versión (nuevo registro)</strong> del certificado con los cambios aplicados en la tabla.<br><br>
                                                                    La versión antigua que estabas viendo no se perderá y seguirá estando disponible en el historial de opciones.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold text-white shadow-sm" onclick="document.getElementById('formEditor_{{ $certId }}').submit(); this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin me-2\'></i> Guardando...';">Confirmar y Guardar</button>
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
