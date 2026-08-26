<x-base-layout>
    {{-- ==========================================
         1. ESTILOS LOCALES
         ========================================== --}}
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-secondary { background-color: #f1f5f9 !important; color: #475569 !important; border: none; }

        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }

        .nav-tabs-custom .nav-link {
            border: none;
            color: #616161;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .nav-tabs-custom .nav-link.active {
            color: #0052cc;
            background: transparent;
            border-bottom: 3px solid #0052cc;
        }

        .fs-7 { font-size: 0.9rem; }
        .fs-8 { font-size: 0.8rem; }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }
        .hover-opacity:hover { opacity: 0.8; transition: opacity 0.2s; }

        /* Estilos Acordeón de Líneas */
        .accordion-custom .accordion-button:not(.collapsed) {
            background-color: #f8fafc;
            color: #0f172a;
            box-shadow: none;
        }
        .accordion-custom .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,0.1);
        }
        .accordion-custom .accordion-button::after {
            background-size: 1.25rem;
            transition: all 0.3s ease;
        }
        .accordion-custom .accordion-item {
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .accordion-custom .accordion-item:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }
        .table-inner th { font-weight: 600; color: #64748b; font-size: 0.75rem; background-color: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px;}

        /* Círculo destacado para la Cuota */
        .cuota-badge {
            width: 38px;
            height: 38px;
            font-size: 1rem;
            background: linear-gradient(135deg, #4a90e2, #0052cc);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 82, 204, 0.2);
        }
    </style>

    <div class="app-container py-4">

        {{-- ==========================================
             2. ENCABEZADO Y BOTONES DE ACCIÓN
             ========================================== --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <a href="{{ route('certificados.operaciones.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block fw-semibold hover-opacity">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la matriz
                </a>
                <h1 class="h3 fw-bold m-0" style="color: #2c3e50;">
                    Operación <span class="text-primary">{{ $operacion->numero_radicado }}</span>
                </h1>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTipo">
                    <i class="fas fa-tags me-2"></i> Asignar Tipo
                </button>
                <button type="button" class="btn btn-warning shadow-sm rounded-pill px-4 fw-bold text-dark d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTransicionar">
                    <i class="fas fa-exchange-alt me-2"></i> Cambiar Estado
                </button>
                <button type="button" class="btn btn-info shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAlerta">
                    <i class="fas fa-bell me-2"></i> Programar Alerta
                </button>

                {{-- NUEVO BOTÓN: GENERAR CERTIFICADO INDIVIDUAL --}}
                <a href="{{ route('certificados.operaciones.pdf_individual', $operacion->id) }}" target="_blank" class="btn btn-danger shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center">
                    <i class="fas fa-file-pdf me-2"></i> Generar Certificado
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
            {{-- ==========================================
                 3. COLUMNA IZQUIERDA
                 ========================================== --}}
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
                            <span class="badge bg-pastel-primary rounded-pill px-3">BLQ-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 fs-7">
                            <span class="text-muted">Fecha Creación Lote:</span>
                            <span class="fw-semibold text-dark">
                                {{ $operacion->created_at ? $operacion->created_at->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 4. COLUMNA DERECHA: PESTAÑAS (TABS)
                 ========================================== --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card card-custom shadow-sm border-0 h-100">

                    <div class="card-header bg-white pt-3 pb-0 border-bottom px-4" style="border-radius: 20px 20px 0 0;">
                        <ul class="nav nav-tabs nav-tabs-custom" id="operacionTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="lineas-tab" data-bs-toggle="tab" data-bs-target="#lineas" type="button" role="tab">
                                    <i class="fas fa-sitemap me-2"></i> Líneas & Créditos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="alertas-tab" data-bs-toggle="tab" data-bs-target="#alertas" type="button" role="tab">
                                    <i class="fas fa-bell me-2"></i> Alertas
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab">
                                    <i class="fas fa-history me-2"></i> Historial ETL
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-danger" id="certificados-tab" data-bs-toggle="tab" data-bs-target="#certificados" type="button" role="tab">
                                    <i class="fas fa-file-pdf me-2"></i> Certificados
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="operacionTabsContent">

                            {{-- ==========================================
                                 TAB 1: LÍNEAS Y FACTURAS (Destacando Cuota)
                                 ========================================== --}}
                            <div class="tab-pane fade show active" id="lineas" role="tabpanel">

                                @if($lineasAgrupadas->count() > 0)
                                    <div class="accordion accordion-custom" id="accordionLineas">
                                        @foreach($lineasAgrupadas as $nombreLinea => $facturas)
                                            @php
                                                $totalLinea = $facturas->sum('valor');
                                                // ORDENAMOS LAS FACTURAS POR CUOTA DE FORMA ASCENDENTE
                                                $facturasOrdenadas = $facturas->sortBy('cuota');
                                            @endphp
                                            <div class="accordion-item border-0 mb-3 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                                                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} bg-white border-0 shadow-none px-4 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                                        <div class="d-flex align-items-center w-100 me-3">
                                                            <div class="bg-pastel-primary rounded-circle d-flex justify-content-center align-items-center me-3 flex-shrink-0" style="width: 48px; height: 48px;">
                                                                <i class="fas fa-box-open text-primary fs-5"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="fw-bold text-dark mb-1 fs-5">{{ $nombreLinea }}</h6>
                                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                                    <span class="badge bg-pastel-info text-dark px-2 py-1"><i class="fas fa-file-invoice me-1 opacity-50"></i> {{ $facturas->count() }} Facturas</span>
                                                                    <span class="badge bg-pastel-success text-dark px-2 py-1"><i class="fas fa-dollar-sign me-1 opacity-50"></i> Total: ${{ number_format((float)$totalLinea, 2) }}</span>
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
                                                                    @foreach($facturasOrdenadas as $factura)
                                                                        <tr>
                                                                            <td class="ps-4 fw-bold text-dark border-bottom py-3" style="font-family: monospace;">
                                                                                #{{ $factura->id_factura ?? 'N/A' }}
                                                                            </td>
                                                                            <td class="text-center border-bottom py-3">
                                                                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold cuota-badge" title="Número de Cuota">
                                                                                    {{ $factura->cuota ?? '-' }}
                                                                                </span>
                                                                            </td>
                                                                            <td class="border-bottom py-3">
                                                                                <div class="fw-semibold text-dark">{{ $factura->pagare ?? 'Sin Pagaré' }}</div>
                                                                            </td>
                                                                            <td class="border-bottom py-3">
                                                                                @if($factura->fecha_venci)
                                                                                    @php
                                                                                        $fechaV = \Carbon\Carbon::parse($factura->fecha_venci);
                                                                                        $diasMora = now()->diffInDays($fechaV, false);
                                                                                    @endphp
                                                                                    <div class="text-dark fw-semibold"><i class="far fa-calendar-alt text-muted me-1"></i> {{ $fechaV->format('d/m/Y') }}</div>
                                                                                    @if($diasMora < 0)
                                                                                        <div class="text-danger fs-8 fw-bold mt-1">{{ abs(intval($diasMora)) }} días vencidos</div>
                                                                                    @endif
                                                                                @else
                                                                                    <span class="text-muted">No Definida</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-end border-bottom py-3">
                                                                                <div class="fw-bold fs-6" style="color: #047857;">${{ number_format((float)$factura->valor, 2) }}</div>
                                                                                <div class="text-muted fs-8" title="Valor Bruto/Inicial">Bruto: ${{ number_format((float)$factura->valor_inicial, 2) }}</div>
                                                                            </td>
                                                                            <td class="text-center pe-4 border-bottom py-3">
                                                                                @if($factura->estado == 'PROCESADO')
                                                                                    <span class="badge bg-pastel-success text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                                                                                        <i class="fas fa-check-circle me-1"></i> {{ $factura->estado }}
                                                                                    </span>
                                                                                @elseif($factura->anular == 1)
                                                                                    <span class="badge bg-pastel-warning text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1">
                                                                                        <i class="fas fa-ban me-1"></i> ANULADO
                                                                                    </span>
                                                                                @else
                                                                                    <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25 rounded-pill px-3 py-1">
                                                                                        <i class="fas fa-hourglass-half me-1"></i> PENDIENTE
                                                                                    </span>
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
                                        <p class="mb-0 fs-7">No se encontraron facturas asociadas a las cuentas de este cliente (Cédula: <strong>{{ $operacion->id_tercero }}</strong>) en el Bloque <strong>{{ $operacion->numero_bloque }}</strong>.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 2: ALERTAS --}}
                            <div class="tab-pane fade" id="alertas" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-clock me-2"></i> Registro de Alertas</h6>
                                    <button class="btn btn-sm btn-outline-info rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalAlerta">
                                        <i class="fas fa-plus me-1"></i> Nueva Alerta
                                    </button>
                                </div>

                                @if($historialAlertas->count() > 0)
                                    <div class="row g-3">
                                        @foreach($historialAlertas as $alerta)
                                            @php $esAlertaBloque = is_null($alerta->id_car_sia_operaciones); @endphp
                                            <div class="col-12">
                                                <div class="p-3 border rounded-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center bg-white shadow-sm hover-opacity">
                                                    <div class="d-flex align-items-center gap-3 mb-2 mb-sm-0">
                                                        <div class="bg-pastel-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px;">
                                                            <i class="fas fa-bell text-info fs-5"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                                                {{ $alerta->tipoAlerta->nombre ?? 'Tipo de Alerta Desconocido' }}
                                                                @if($esAlertaBloque)
                                                                    <span class="badge bg-pastel-primary px-2 py-1" style="font-size: 0.65rem;" title="Alerta de Lote"><i class="fas fa-layer-group"></i> Lote</span>
                                                                @else
                                                                    <span class="badge bg-pastel-secondary text-dark px-2 py-1 border" style="font-size: 0.65rem;" title="Alerta Individual"><i class="fas fa-user"></i> Cliente</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-muted fs-8 mt-1"><i class="far fa-calendar-alt me-1"></i> Programada para: <span class="fw-semibold text-dark">{{ $alerta->fecha_programada ? \Carbon\Carbon::parse($alerta->fecha_programada)->format('d/m/Y') : 'N/A' }}</span></div>
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
                                    {{-- COLUMNA: HISTORIAL DE ESTADOS --}}
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
                                                            @if($esEstadoBloque)
                                                                <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> BLQ-{{ str_pad($historialEstado->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                            @else
                                                                <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-user me-1"></i> Individual</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted fs-8 mt-1"><i class="far fa-clock me-1"></i> {{ $historialEstado->created_at ? $historialEstado->created_at->format('d M, Y h:i A') : 'Fecha no disponible' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bg-light p-3 rounded-4 text-center text-muted fs-8">Sin historial de estados registrado.</div>
                                        @endif
                                    </div>

                                    {{-- COLUMNA: HISTORIAL DE TIPOS/EVENTOS --}}
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-muted mb-4 fs-8 text-uppercase"><i class="fas fa-tags me-2"></i> Eventos Inyectados</h6>
                                        @if($historialTipos->count() > 0)
                                            <div class="border-start border-2 border-info border-opacity-25 ms-3 ps-4 position-relative">
                                                @foreach($historialTipos as $historialTipo)
                                                    @php $esTipoBloque = is_null($historialTipo->id_car_sia_operaciones); @endphp
                                                    <div class="mb-4 position-relative">
                                                        <span class="position-absolute bg-white border border-2 border-info rounded-circle" style="width: 14px; height: 14px; left: -1.8rem; top: 0.25rem;"></span>
                                                        <div class="fw-bold text-dark d-flex align-items-center gap-2 flex-wrap">
                                                            {{ $historialTipo->tipo->nombre ?? 'Tipo Desconocido' }}
                                                            @if($esTipoBloque)
                                                                <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> BLQ-{{ str_pad($historialTipo->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
                                                            @else
                                                                <span class="badge bg-pastel-secondary text-dark border border-secondary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-user me-1"></i> Individual</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-muted fs-8 mt-1"><i class="far fa-clock me-1"></i> {{ $historialTipo->created_at ? $historialTipo->created_at->format('d M, Y h:i A') : 'Fecha no disponible' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bg-light p-3 rounded-4 text-center text-muted fs-8">Sin eventos inyectados.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- ==========================================
                                 TAB 4: CERTIFICADOS (Visor de PDF)
                                 ========================================== --}}
                            <div class="tab-pane fade" id="certificados" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase">
                                        <i class="fas fa-search me-2"></i> Vista Previa del Certificado
                                    </h6>

                                    {{-- Botón para abrir el PDF a pantalla completa en otra pestaña --}}
                                    <a href="{{ route('certificados.operaciones.pdf_individual', $operacion->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill fw-bold">
                                        <i class="fas fa-external-link-alt me-1"></i> Pantalla completa
                                    </a>
                                </div>

                                {{-- Contenedor del Iframe --}}
                                <div class="border-0 rounded-4 overflow-hidden shadow-sm bg-light" style="height: 650px; position: relative;">
                                    {{-- Iframe que carga dinámicamente el método generarIndividual del controlador --}}
                                    <iframe
                                        src="{{ route('certificados.operaciones.pdf_individual', $operacion->id) }}"
                                        width="100%"
                                        height="100%"
                                        frameborder="0"
                                        style="border: none; background-color: #f8fafc;">
                                        <p>Tu navegador no soporta la visualización de PDFs.
                                           <a href="{{ route('certificados.operaciones.pdf_individual', $operacion->id) }}">Descargar PDF</a>
                                        </p>
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==========================================
         5. MODALES (ACCIONES INDIVIDUALES)
         ========================================== --}}

    {{-- MODAL 1: ASIGNAR TIPO --}}
    <div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.operaciones.asignar_tipo', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-tags text-primary me-2"></i> Asignar Tipo / Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-pastel-secondary text-dark border-0 rounded-4 mb-4" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i> Este evento quedará asignado de forma <strong>individual</strong> a esta operación.
                    </div>
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3">
                        <label for="id_car_sia_tipos" class="form-label fw-semibold text-muted">Seleccionar tipo o evento</label>
                        <select name="id_car_sia_tipos" id="id_car_sia_tipos" class="form-select bg-light border-0" required>
                            <option value="">Seleccione una opción</option>
                            @isset($tipos)
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold text-white">Guardar tipo</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: CAMBIAR ESTADO --}}
    <div class="modal fade" id="modalTransicionar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.operaciones.transicionar', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-exchange-alt text-warning me-2"></i> Cambiar Estado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-pastel-secondary text-dark border-0 rounded-4 mb-4" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i> Este estado quedará registrado de forma <strong>individual</strong> para este cliente.
                    </div>
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3">
                        <label for="id_car_sia_estados" class="form-label fw-semibold text-muted">Estado de la operación</label>
                        <select name="id_car_sia_estados" id="id_car_sia_estados" class="form-select bg-light border-0" required>
                            <option value="">Seleccione un estado</option>
                            @isset($estados)
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            @endisset
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

    {{-- MODAL 3: PROGRAMAR ALERTA --}}
    <div class="modal fade" id="modalAlerta" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.operaciones.programar_alerta', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-bell text-info me-2"></i> Programar Alerta Específica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-pastel-secondary text-dark border-0 rounded-4 mb-4" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i> Esta alerta será <strong>individual</strong> y solo afectará a esta operación (Radicado: {{ $operacion->numero_radicado }}).
                    </div>
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3">
                        <label for="id_car_sia_tipos_alerta" class="form-label fw-semibold text-muted">Tipo de alerta</label>
                        <select name="id_car_sia_tipos_alerta" id="id_car_sia_tipos_alerta" class="form-select bg-light border-0" required>
                            <option value="">Seleccione una alerta</option>
                            @isset($tiposAlerta)
                                @foreach($tiposAlerta as $tipoAlerta)
                                    <option value="{{ $tipoAlerta->id }}">{{ $tipoAlerta->nombre }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="mb-0">
                        <label for="fecha_programada" class="form-label fw-semibold text-muted">Fecha programada</label>
                        <input type="date" name="fecha_programada" id="fecha_programada" class="form-control bg-light border-0" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold text-white">Programar Alerta</button>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>
