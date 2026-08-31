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
        .bg-pastel-danger { background-color: #fee2e2 !important; color: #ef4444 !important; border: none; }

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

        /* Estilos para modo Hoja de Cálculo */
        .table-spreadsheet { border-collapse: collapse; }
        .table-spreadsheet th { font-size: 0.75rem; text-transform: uppercase; color: #64748b; border: 1px solid #e2e8f0; padding: 1rem 0.75rem; }
        .table-spreadsheet td { padding: 0; border: 1px solid #e2e8f0; vertical-align: middle; }
        .input-spreadsheet { width: 100%; border: none; padding: 0.85rem 0.75rem; background: transparent; outline: none; font-size: 0.85rem; color: #0f172a; transition: all 0.2s; }
        .input-spreadsheet:focus { background-color: #f0fdf4; box-shadow: inset 0 0 0 2px #22c55e; }
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
                <button type="button" class="btn btn-warning shadow-sm rounded-pill px-4 fw-bold text-dark d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTransicionar">
                    <i class="fas fa-exchange-alt me-2"></i> Cambiar Estado
                </button>

                <button type="button" class="btn btn-info shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAlerta">
                    <i class="fas fa-bell me-2"></i> Programar Alerta
                </button>

                {{-- BOTÓN UNIFICADO: GENERAR Y ASIGNAR TIPO --}}
                <button type="button" class="btn btn-danger shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTipo">
                    <i class="fas fa-file-pdf me-2"></i> Generar Certificado
                </button>
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

                            {{-- TAB 1: LÍNEAS Y FACTURAS --}}
                            <div class="tab-pane fade show active" id="lineas" role="tabpanel">
                                @if($lineasAgrupadas->count() > 0)
                                    <div class="accordion accordion-custom" id="accordionLineas">
                                        @foreach($lineasAgrupadas as $nombreLinea => $facturas)
                                            @php
                                                $totalLinea = $facturas->sum('valor');
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
                                                                <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> API-{{ str_pad($historialEstado->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
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
                                                                <span class="badge bg-pastel-primary text-primary border border-primary border-opacity-25" style="font-size: 0.65rem;"><i class="fas fa-layer-group me-1"></i> API-{{ str_pad($historialTipo->numero_bloque, 4, '0', STR_PAD_LEFT) }}</span>
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

                            {{-- TAB 4: CERTIFICADOS --}}
                            <div class="tab-pane fade" id="certificados" role="tabpanel">

                                <div class="accordion accordion-custom" id="accordionCertificados">
                                    <div class="accordion-item border-0 mb-3 bg-white shadow-sm" style="border-radius: 15px; overflow: hidden;">
                                        <h2 class="accordion-header" id="headingCertificado1">
                                            <button class="accordion-button collapsed bg-white border-0 shadow-none px-4 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCertificado1">
                                                <div class="d-flex align-items-center w-100 me-3">
                                                    <div class="bg-pastel-danger rounded-circle d-flex justify-content-center align-items-center me-3 flex-shrink-0" style="width: 48px; height: 48px;">
                                                        <i class="fas fa-file-pdf text-danger fs-5"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="fw-bold text-dark mb-1 fs-5">Certificado: Estado de Cuenta (Al Día)</h6>
                                                        <div class="text-muted fs-8 mt-1">Datos procesados correspondientes al Lote API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}</div>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>

                                        <div id="collapseCertificado1" class="accordion-collapse collapse" data-bs-parent="#accordionCertificados">
                                            <div class="accordion-body p-4 border-top">

                                                @if($operacion->lineas && $operacion->lineas->count() > 0)
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-sliders-h me-2"></i> Controles del Documento</h6>
                                                        <div class="btn-group shadow-sm bg-white rounded-pill p-1" role="group">
                                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 active fw-bold" id="btnModePdf" onclick="toggleMode('pdf')">
                                                                <i class="fas fa-file-pdf me-1"></i> Visor PDF
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-light text-success rounded-pill px-3 fw-bold" id="btnModeData" onclick="toggleMode('data')">
                                                                <i class="fas fa-table me-1"></i> Modo Hoja de Cálculo
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {{-- MODO A: VISOR PDF --}}
                                                    <div id="pdfViewerContainer" class="border-0 rounded-4 overflow-hidden shadow-sm bg-light transition-all" style="height: 650px; position: relative;">
                                                        <iframe src="{{ route('certificados.operaciones.pdf_individual', $operacion->id) }}" width="100%" height="100%" frameborder="0" style="border: none; background-color: #f8fafc;"></iframe>
                                                    </div>

                                                    {{-- MODO B: HOJA DE CÁLCULO ACTUALIZADA --}}
                                                    <div id="dataEditorContainer" class="d-none transition-all">
                                                        <form action="{{ route('certificados.operaciones.actualizar_lineas', $operacion->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="table-responsive rounded-4 shadow-sm border mb-3 overflow-hidden" style="max-height: 500px;">
                                                                <table class="table table-spreadsheet align-middle mb-0 text-nowrap">
                                                                    <thead class="bg-light" style="position: sticky; top: 0; z-index: 10;">
                                                                        <tr>
                                                                            <th class="ps-3" style="width: 100px;">N° Factura</th>
                                                                            <th style="width: 120px;">Línea (Cuenta)</th>
                                                                            <th style="width: 140px;" class="text-center">Auditor / Tipo</th>
                                                                            <th style="width: 140px;" class="text-center">Firma (Hash)</th>
                                                                            <th style="width: 130px;">Calificación</th>
                                                                            <th style="width: 100px;">Días Mora</th>
                                                                            <th style="width: 130px;">Vencimiento</th>
                                                                            <th>Observación General</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($operacion->lineas as $linea)
                                                                            <tr>
                                                                                <td class="ps-3 bg-light fw-bold text-muted border-end">#{{ $linea->id_factura }}</td>
                                                                                <td class="bg-light text-muted border-end">{{ $linea->id_car_sia_lineas }}</td>
                                                                                <td class="bg-light border-end px-2">
                                                                                    <div class="d-flex flex-column gap-1 align-items-center">
                                                                                        @if($linea->usuario)
                                                                                            <span class="badge bg-pastel-primary text-primary text-truncate w-100" style="max-width: 130px;" title="Auditor: {{ $linea->usuario->name }}"><i class="fas fa-user-shield me-1"></i> {{ strtok($linea->usuario->name, ' ') }}</span>
                                                                                        @else
                                                                                            <span class="badge bg-pastel-secondary text-muted w-100"><i class="fas fa-robot me-1"></i> Automático</span>
                                                                                        @endif

                                                                                        @if($linea->tipoAuditoria)
                                                                                            <span class="badge bg-pastel-info text-info text-truncate w-100" style="max-width: 130px;" title="Tipo: {{ $linea->tipoAuditoria->nombre }}"><i class="fas fa-tag me-1"></i> {{ $linea->tipoAuditoria->nombre }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                                <td class="bg-light text-center border-end">
                                                                                    @if($linea->hash_certificado)
                                                                                        <span class="font-monospace text-muted" style="font-size: 0.7rem; cursor: help;" title="{{ $linea->hash_certificado }}">
                                                                                            <i class="fas fa-fingerprint text-info me-1"></i>...{{ substr($linea->hash_certificado, -12) }}
                                                                                        </span>
                                                                                    @else
                                                                                        <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <select name="lineas[{{ $linea->id }}][calificacion]" class="input-spreadsheet">
                                                                                        <option value="Bueno" {{ $linea->calificacion == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                                                                        <option value="Regular" {{ $linea->calificacion == 'Regular' ? 'selected' : '' }}>Regular</option>
                                                                                        <option value="Irregular" {{ $linea->calificacion == 'Irregular' ? 'selected' : '' }}>Irregular</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" name="lineas[{{ $linea->id }}][dias_mora_automaticos]" value="{{ $linea->dias_mora_automaticos }}" class="input-spreadsheet text-center">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="date" name="lineas[{{ $linea->id }}][fecha_venci]" value="{{ $linea->fecha_venci ? $linea->fecha_venci->format('Y-m-d') : '' }}" class="input-spreadsheet">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="lineas[{{ $linea->id }}][observacion]" value="{{ $linea->observacion }}" class="input-spreadsheet" placeholder="Escribe una observación...">
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="d-flex justify-content-end bg-pastel-secondary p-3 rounded-4 align-items-center">
                                                                <span class="text-muted fs-8 me-3"><i class="fas fa-info-circle me-1"></i> Los cambios aplicados registrarán tu autoría, actualizarán el hash y reconstruirán el PDF.</span>
                                                                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold text-white shadow-sm hover-opacity">
                                                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed">
                                                        <i class="fas fa-file-excel fs-1 text-secondary mb-3 opacity-25"></i>
                                                        <h6 class="fw-bold text-dark">Líneas No Estructuradas</h6>
                                                        <p class="mb-0 fs-7">El certificado no cuenta con datos procesados aún. Utiliza el botón <strong class="text-danger">Generar Certificado</strong> en la cabecera para inicializar los datos y asignar el tipo.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
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

    {{-- MODAL UNIFICADO: GENERAR Y ASIGNAR TIPO --}}
    <div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('certificados.operaciones.asignar_tipo', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-file-pdf text-danger me-2"></i> Generar Certificado / Asignar Tipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- ALERTA DINÁMICA: DETECTAR SI ES PRIMER CERTIFICADO O UNA ACTUALIZACIÓN --}}
                    @if(isset($operacion->lineas) && $operacion->lineas->count() > 0)
                        <div class="alert bg-pastel-info text-dark border-0 rounded-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle text-info fs-3 me-3"></i>
                                <div>
                                    <strong class="d-block fs-6">Actualización de Certificado</strong>
                                    <span class="fs-8">Ya existe un certificado previo para este cliente. Al asignar un nuevo tipo, se <strong>regenerará la versión</strong> y se actualizará el Hash de auditoría.</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert bg-pastel-warning text-dark border-0 rounded-4 mb-4 shadow-sm border-start border-4 border-warning">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning fs-3 me-3"></i>
                                <div>
                                    <strong class="d-block fs-6">¡Primer Certificado!</strong>
                                    <span class="fs-8">Este será el primer certificado generado para esta operación. Selecciona el evento para inicializar los datos en el documento PDF.</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">
                    <div class="mb-3 mt-2">
                        <label for="id_car_sia_tipos" class="form-label fw-semibold text-muted">Seleccionar tipo o evento del certificado</label>
                        <select name="id_car_sia_tipos" id="id_car_sia_tipos" class="form-select form-select-lg bg-light border-0" required>
                            <option value="">Seleccione una opción...</option>
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
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold text-white shadow-sm">Generar y Asignar</button>
                </div>
            </form>
        </div>
    </div>

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

    <script>
        function toggleMode(mode) {
            const btnPdf = document.getElementById('btnModePdf');
            const btnData = document.getElementById('btnModeData');
            const containerPdf = document.getElementById('pdfViewerContainer');
            const containerData = document.getElementById('dataEditorContainer');

            if (mode === 'pdf') {
                btnPdf.classList.replace('btn-light', 'btn-danger');
                btnPdf.classList.replace('text-danger', 'text-white');

                btnData.classList.replace('btn-success', 'btn-light');
                btnData.classList.replace('text-white', 'text-success');

                containerPdf.classList.remove('d-none');
                containerData.classList.add('d-none');
            } else {
                btnData.classList.replace('btn-light', 'btn-success');
                btnData.classList.replace('text-success', 'text-white');

                btnPdf.classList.replace('btn-danger', 'btn-light');
                btnPdf.classList.replace('text-white', 'text-danger');

                containerData.classList.remove('d-none');
                containerPdf.classList.add('d-none');
            }
        }
    </script>
</x-base-layout>
