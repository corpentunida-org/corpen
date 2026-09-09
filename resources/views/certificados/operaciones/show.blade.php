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
            <!-- 2. BOTONES DE ACCIÓN (INFORME CLIENTE)     -->
            <!-- ========================================== -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('certificados.operaciones.informe_cliente', $operacion->id) }}"
                    target="_blank"
                    class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center hover-opacity"
                    onclick="bloquearBotonUI(this, 'Generando...')">
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

                {{-- MENU  Info Pastor--}}
                <div class="card card-custom shadow-sm mb-4 border-0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <div class="symbol-label bg-pastel-primary me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 15px;">
                                    <i class="fas fa-user-tie text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">Datos del Cliente</h5>
                                    <span class="text-muted fs-8">Información de Maestras</span>
                                </div>
                            </div>
                            @if($operacion->tercero)
                                <button type="button" class="btn btn-sm btn-light text-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditarTercero">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </button>
                            @endif
                        </div>

                        @if($operacion->tercero)
                            <div class="bg-light rounded-4 p-3 mb-3">
                                <div class="fw-bolder text-dark fs-6">{{ $operacion->tercero->nom_ter }}</div>
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
                            <div class="d-flex justify-content-between mb-2 fs-7">
                                <span class="text-muted"><i class="fas fa-map-marker-alt me-2 opacity-50"></i>Ciudad:</span>
                                <span class="fw-semibold text-dark">{{ $operacion->tercero->ciudad ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between fs-7">
                                <span class="text-muted"><i class="fas fa-map me-2 opacity-50"></i>Dirección:</span>
                                <span class="fw-semibold text-dark text-end ms-3">{{ $operacion->tercero->dir ?? 'N/A' }}</span>
                            </div>
                        @else
                            <div class="alert bg-pastel-warning text-center border-0 rounded-4">Tercero no encontrado en maestras.</div>
                        @endif
                    </div>
                </div>

                {{-- Modal para Editar Tercero --}}
                @if($operacion->tercero)
                <div class="modal fade" id="modalEditarTercero" tabindex="-1" aria-labelledby="modalEditarTerceroLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light border-bottom-0">
                                <h5 class="modal-title fw-bold text-dark" id="modalEditarTerceroLabel">
                                    <i class="fas fa-user-edit text-primary me-2"></i>Actualizar Datos del Cliente
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('certificados.operaciones.actualizar_tercero', $operacion->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body p-4">
                                    <div class="alert alert-info bg-pastel-info border-0 fs-7 mb-4">
                                        <i class="fas fa-info-circle me-2"></i> El "Nombre Completo" se autogenerará al guardar uniendo los nombres y apellidos.
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Primer Nombre</label>
                                            <input type="text" class="form-control" name="nom1" value="{{ $operacion->tercero->nom1 }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Segundo Nombre</label>
                                            <input type="text" class="form-control" name="nom2" value="{{ $operacion->tercero->nom2 }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Primer Apellido</label>
                                            <input type="text" class="form-control" name="apl1" value="{{ $operacion->tercero->apl1 }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Segundo Apellido</label>
                                            <input type="text" class="form-control" name="apl2" value="{{ $operacion->tercero->apl2 }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Teléfono Principal (tel)</label>
                                            <input type="text" class="form-control" name="tel" value="{{ $operacion->tercero->tel }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Teléfono Secundario (tel1)</label>
                                            <input type="text" class="form-control" name="tel1" value="{{ $operacion->tercero->tel1 }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Correo Electrónico</label>
                                            <input type="email" class="form-control" name="email" value="{{ $operacion->tercero->email }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label text-muted fs-8 fw-bold text-uppercase">Dirección</label>
                                            <input type="text" class="form-control" name="dir" value="{{ $operacion->tercero->dir }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary fw-bold">
                                        <i class="fas fa-save me-1"></i> Actualizar y Concatenar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- MENU  Info Técnica (ETL)--}}
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


                {{-- CAJA PRINCIPAL: MATRIZ DE TRAZABILIDAD (AUDITORÍA)--}}
                <div class="card card-custom p-3 shadow-sm d-flex flex-column mt-4" style="flex: 1; min-height: 0;">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                            <i class="fas fa-shield-alt text-muted"></i> Matriz de Trazabilidad
                        </h5>
                    </div>

                    <p class="text-muted mb-3" style="font-size:.8rem;">Registro de eventos y trazabilidad del sistema.</p>

                    <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2">
                        @if(isset($logsAuditoria) && $logsAuditoria->count() > 0)
                            <div class="position-relative ms-2" style="border-left: 2px solid var(--c-border, #dee2e6);">
                                @foreach($logsAuditoria as $log)

                                    {{-- REGLA DE PRIORIZACIÓN: Es del bloque general (nulo) o es de mi operación --}}
                                    @if(is_null($log->id_car_sia_operaciones) || $log->id_car_sia_operaciones == $operacion->id)

                                        <div class="position-relative mb-3 ps-3 pt-1">
                                            {{-- Punto del Timeline --}}
                                            <span class="position-absolute bg-primary rounded-circle border border-2 border-white shadow-sm" style="width: 12px; height: 12px; left: -7px; top: 8px;"></span>

                                            {{-- Tarjeta del Log --}}
                                            <div class="p-2 rounded bg-light border border-light shadow-sm">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="fw-bold text-dark" style="font-size: .75rem; line-height: 1.2;">
                                                        {{ $log->tituloEvento ?? 'Evento de Sistema' }}
                                                    </span>
                                                    <span class="text-muted" style="font-size: .65rem; white-space: nowrap;">
                                                        {{ $log->fechaEvento ?? '—' }}
                                                    </span>
                                                </div>

                                                <div class="text-muted mb-2 d-flex justify-content-between align-items-center" style="font-size: .7rem;">
                                                    <div>
                                                        <i class="fas fa-user-circle me-1 opacity-50"></i>
                                                        <span class="fw-medium text-dark">{{ $log->nombreUsuario ?? 'Sistema Automático' }}</span>
                                                        @if(!empty($log->cargoUsuario))
                                                            <span class="fst-italic opacity-75">({{ $log->cargoUsuario }})</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($log->ipDelUsuario))
                                                        <span class="text-muted" style="font-size: .6rem; font-family: monospace;" title="IP de origen">
                                                            {{ $log->ipDelUsuario }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-white text-secondary border shadow-none" style="font-size: .6rem; padding: .2rem .4rem;">
                                                            <i class="fas fa-desktop me-1"></i> {{ $log->origenEvento ?? 'Sistema' }}
                                                        </span>

                                                        {{-- DIFERENCIACIÓN VISUAL POR ORIGEN DEL LOG --}}
                                                        @if(is_null($log->id_car_sia_operaciones))
                                                            <span class="badge bg-light text-primary border border-primary border-opacity-25 shadow-none ms-1" style="font-size: .6rem; padding: .2rem .4rem;" title="Evento a nivel de Lote/Bloque">
                                                                <i class="fas fa-layer-group me-1"></i> Lote General
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-success border border-success border-opacity-25 shadow-none ms-1" style="font-size: .6rem; padding: .2rem .4rem;" title="Acción directa en esta operación">
                                                                <i class="fas fa-user-check me-1"></i> Individual
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($log->hayDetalles && isset($log->detalles_procesados) && count($log->detalles_procesados) > 0)
                                                        <button type="button" class="btn btn-sm text-primary p-0 m-0 border-0 bg-transparent fw-medium d-flex align-items-center gap-1" style="font-size: .65rem;" onclick="document.getElementById('detalles-trazabilidad-{{ $loop->index }}').classList.toggle('d-none')">
                                                            <i class="fas fa-search-plus"></i> Detalles
                                                        </button>
                                                    @endif
                                                </div>

                                                {{-- Contenedor de Detalles Oculto --}}
                                                @if($log->hayDetalles && isset($log->detalles_procesados) && count($log->detalles_procesados) > 0)
                                                    <div id="detalles-trazabilidad-{{ $loop->index }}" class="d-none mt-2 pt-2 border-top border-light">
                                                        <div class="p-2 bg-white rounded border border-secondary border-opacity-10 text-wrap text-break" style="font-size: 0.65rem;">
                                                            @foreach($log->detalles_procesados as $llave => $valor)
                                                                @php
                                                                    $esJson = is_string($valor) && is_array(json_decode($valor, true)) && json_last_error() === JSON_ERROR_NONE;
                                                                    $datosParseados = $esJson ? json_decode($valor, true) : $valor;

                                                                    if (is_array($datosParseados) || is_object($datosParseados)) {
                                                                        $datosParseados = collect($datosParseados)->filter(function($v, $k) use ($llave) {
                                                                            if (strtolower($llave) === 'metricas' && $v === 0) return false;
                                                                            return !is_null($v) && $v !== '';
                                                                        })->toArray();
                                                                    }
                                                                @endphp

                                                                @if((is_array($datosParseados) && count($datosParseados) > 0) || (!is_array($datosParseados) && $datosParseados !== '' && $datosParseados !== null))
                                                                    <div class="mb-2 border-bottom border-light pb-1">
                                                                        <strong class="text-primary opacity-75 text-uppercase d-block mb-1" style="font-size: 0.55rem; letter-spacing: 0.5px;">
                                                                            {{ str_replace('_', ' ', $llave) }}
                                                                        </strong>

                                                                        @if(is_array($datosParseados))
                                                                            <div class="d-flex flex-wrap gap-1">
                                                                                @foreach($datosParseados as $subKey => $subVal)
                                                                                    <span class="badge bg-light text-dark border border-secondary border-opacity-25" style="font-size: 0.6rem; font-weight: 500;">
                                                                                        <span class="text-muted">{{ str_replace('_', ' ', ucfirst($subKey)) }}:</span>
                                                                                        <span class="fw-bold">{{ is_array($subVal) ? json_encode($subVal, JSON_UNESCAPED_UNICODE) : $subVal }}</span>
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <span class="text-dark fw-medium" style="font-size: 0.65rem;">
                                                                                {{ $datosParseados }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                {{-- Fin Detalles --}}

                                            </div>
                                        </div>

                                    @endif {{-- Fin validación de prioridad --}}
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center h-100">
                                <i class="fas fa-clipboard-list fs-2 mb-3 text-secondary opacity-25"></i>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: .9rem;">Sin Movimientos</h6>
                                <p class="text-muted mb-0" style="font-size: .75rem; max-width: 200px;">No hay registros de auditoría.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card card-custom shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-0 border-bottom px-4" style="border-radius: 20px 20px 0 0;">
                        <ul class="nav nav-tabs nav-tabs-custom border-0 d-flex flex-nowrap overflow-auto" id="operacionTabs" role="tablist" style="scrollbar-width: none;">

                            <!-- Parámetros -->
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

                            <!-- Historial / Estado -->
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
                            {{-- TAB 0: PARÁMETROS --}}
                            <div class="tab-pane fade" id="parametros" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-cog me-2"></i> Configuración General</h6>
                                    <button type="button"
                                            class="btn bg-pastel-secondary shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalCrearConfiguracion"> <!-- Cambia esto por el ID exacto de tu modal -->
                                        <i class="fas fa-cog me-2 opacity-75"></i> Parámetros
                                    </button>
                                </div>

                                @if(isset($operacionesConfiguradas) && $operacionesConfiguradas->count() > 0)
                                    <div class="table-responsive border rounded-3 shadow-sm mb-3">
                                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.75rem;">
                                            <thead class="bg-light text-muted text-uppercase" style="font-size: 0.65rem;">
                                                <tr>
                                                    <th class="text-center py-2 border-bottom" style="width: 5%;">Det.</th>
                                                    <th class="py-2 border-bottom" style="width: 15%;">Radicado</th>
                                                    <th class="py-2 border-bottom" style="width: 20%;">Cliente</th>
                                                    <th class="py-2 border-bottom" style="width: 20%;">Acción Vencimiento</th>
                                                    <th class="text-center py-2 border-bottom" style="width: 10%;">Frec.</th>
                                                    <th class="text-center py-2 border-bottom" style="width: 10%;">Est. Acción</th>
                                                    <th class="text-center py-2 border-bottom" style="width: 5%;">Notif.</th>
                                                    <th class="text-center pe-3 py-2 border-bottom" style="width: 15%;">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- ========================================== --}}
                                                {{-- CONFIGURACIONES INDIVIDUALES (EXCEPCIÓN)   --}}
                                                {{-- ========================================== --}}
                                                @foreach($operacionesConfiguradas as $op)
                                                    @php
                                                        // Convertimos a colección para soportar múltiples excepciones por radicado
                                                        $configs = $op->configuracion instanceof \Illuminate\Support\Collection
                                                            ? $op->configuracion
                                                            : collect([$op->configuracion])->filter();
                                                    @endphp

                                                    @foreach($configs as $conf)
                                                        @php
                                                            $hayConfiguradas = true;
                                                            $configBaseOp = $conf->configuracionBase;
                                                            $accionVencOp = $configBaseOp?->accionVencimiento;
                                                            $parametrosOp = $configBaseOp?->parametros;

                                                            // Accedemos al tercero directamente desde $op (la operación)
                                                            $nombreCliente = trim(($op->tercero?->nom_ter ?? 'Sin Tercero') . ' ' . ($op->tercero?->apl1 ?? ''));
                                                        @endphp

                                                        <tr style="background-color: #fff; cursor: pointer; border-bottom: 1px solid #f1f3f5; transition: background-color 0.2s;" class="parent-row {{ !$conf->estado_activo ? 'opacity-75 bg-light' : '' }}" onclick="toggleParametros('det-op-{{ $conf->id }}', this)" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='#fff'">
                                                            <td class="text-center text-info py-2"><i class="fas fa-chevron-circle-down opacity-50 icon-toggle"></i></td>
                                                            <td class="font-monospace fw-bold py-2">{{ $op->numero_radicado ?? 'N/A' }}</td>
                                                            <td class="text-truncate py-2" title="{{ $nombreCliente }}" style="max-width: 150px;">{{ $nombreCliente }}</td>
                                                            <td class="fw-bold text-dark text-truncate py-2" title="{{ $accionVencOp?->nombre ?? 'N/A' }}" style="max-width: 220px;">{{ $accionVencOp?->nombre ?? 'N/A' }}</td>
                                                            <td class="text-center py-2">
                                                                @if($configBaseOp?->frecuencia_recordatorio_dias) <span class="badge bg-light text-dark border">Cada {{ $configBaseOp->frecuencia_recordatorio_dias }} d</span>
                                                                @else <span class="text-muted">—</span> @endif
                                                            </td>
                                                            <td class="text-center py-2">
                                                                @if(isset($accionVencOp?->estado))
                                                                    <span class="badge {{ $accionVencOp->estado ? 'bg-white text-success border border-success' : 'bg-white text-danger border border-danger' }} px-2 py-1">
                                                                        {{ $accionVencOp->estado ? 'ACTIVA' : 'INACTIVA' }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center py-2">
                                                                @if($conf->estado_notificacion) <span class="text-success"><i class="fas fa-bell"></i></span>
                                                                @else <span class="text-muted"><i class="fas fa-bell-slash"></i></span> @endif
                                                            </td>
                                                            <td class="text-center pe-3 py-2">
                                                                @if(method_exists($conf, 'trashed') && $conf->trashed()) <span class="badge bg-danger text-white" style="font-size: 0.60rem;">ELIMINADA</span>
                                                                @elseif(!$conf->estado_activo) <span class="badge bg-secondary text-white" style="font-size: 0.60rem;">INACTIVA</span>
                                                                @else <span class="badge bg-info text-white" style="font-size: 0.60rem;">EXCEPCIÓN</span> @endif
                                                            </td>
                                                        </tr>

                                                        <tr id="det-op-{{ $conf->id }}" style="display: none; background-color: #f4f6f8; box-shadow: inset 0 4px 6px -4px rgba(0,0,0,0.1);">
                                                            <td colspan="8" class="p-0 border-0">
                                                                @php
                                                                    $pOp = is_array($parametrosOp) ? $parametrosOp : (json_decode($parametrosOp, true) ?? []);
                                                                    $claseMoraOp = strtolower($pOp['clasificacion_mora'] ?? 'desconocido');
                                                                    $colorBadgeOp = match($claseMoraOp) {
                                                                        'bueno' => 'bg-pastel-success text-success border-success',
                                                                        'regular' => 'bg-pastel-info text-info border-info',
                                                                        'atencion_especial' => 'bg-pastel-warning text-dark border-warning',
                                                                        'restringido' => 'bg-pastel-danger text-danger border-danger',
                                                                        'irregular' => 'bg-dark text-white border-dark',
                                                                        default => 'bg-light text-secondary border-secondary'
                                                                    };
                                                                @endphp

                                                                <div class="py-2 py-md-3 pe-2 pe-md-4 ps-0 ps-md-1">
                                                                    <div class="ms-2 ms-md-5 bg-white rounded-3 shadow-sm border border-start-0 position-relative w-100" style="border-left: 4px solid #0dcaf0 !important;">
                                                                        <div class="position-absolute text-info d-none d-md-block" style="left: -20px; top: -15px;"><i class="fas fa-level-up-alt fa-rotate-90 fs-5 opacity-25"></i></div>

                                                                        <div class="p-3 w-100">
                                                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 pb-2 border-bottom gap-2">
                                                                                <span class="fw-bold text-info" style="font-size: 0.75rem;"><i class="fas fa-code-branch me-1"></i> Parámetros de Excepción (Rad: {{ $op->numero_radicado }})</span>
                                                                                <span class="text-muted" style="font-size: 0.65rem;">ID Config: {{ $configBaseOp?->id ?? 'N/A' }} | ID Asignación: {{ $conf->id }}</span>
                                                                            </div>

                                                                            <div class="row g-3 align-items-start m-0 w-100">
                                                                                {{-- COLUMNA 1: CLASIFICACIÓN --}}
                                                                                <div class="col-12 col-md-3 position-relative p-0 pe-md-3">
                                                                                    <div class="d-none d-md-block position-absolute end-0 top-0 h-100 border-end"></div>
                                                                                    <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Clasificación</span>
                                                                                    <span class="badge {{ $colorBadgeOp }} border border-opacity-25 px-2 py-1 mb-2 text-wrap"><i class="fas fa-tag me-1"></i> {{ strtoupper(str_replace('_', ' ', $claseMoraOp)) }}</span>
                                                                                    <div class="d-flex flex-column gap-1" style="font-size: 0.75rem;">
                                                                                        <span class="text-dark"><i class="fas fa-calendar-times text-secondary me-1"></i> Max Mora: <strong>{{ $pOp['mora_dias_max'] ?? '0' }}</strong> d</span>
                                                                                        <span class="text-dark"><i class="fas fa-hand-holding-heart text-secondary me-1"></i> Gracia: <strong>{{ $pOp['dias_gracia'] ?? '0' }}</strong> d</span>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- COLUMNA 2: ACTIVADORES --}}
                                                                                <div class="col-12 col-md-5 p-0 ps-md-3 mt-3 mt-md-0 position-relative pe-md-3">
                                                                                    <div class="d-none d-md-block position-absolute end-0 top-0 h-100 border-end"></div>
                                                                                    <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Activadores</span>
                                                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                                                        @if(!empty($pOp['requiere_accion'])) <span class="badge bg-light text-dark border"><i class="fas fa-exclamation-circle text-warning me-1"></i> Acción Restrictiva</span> @endif
                                                                                        @if(!empty($pOp['bloqueo_automatico'])) <span class="badge bg-pastel-danger border border-danger border-opacity-25"><i class="fas fa-ban me-1"></i> Bloqueo Cupo</span> @endif
                                                                                        @if(!empty($pOp['notificacion_gerencia'])) <span class="badge bg-pastel-primary border border-primary border-opacity-25"><i class="fas fa-user-tie me-1"></i> Notifica Gcia.</span> @endif
                                                                                        @if(!empty($pOp['incluir_historico_3_anos'])) <span class="badge bg-light text-secondary border"><i class="fas fa-history me-1"></i> Histórico 3A</span> @endif
                                                                                        @if(empty($pOp['requiere_accion']) && empty($pOp['bloqueo_automatico']) && empty($pOp['notificacion_gerencia'])) <span class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-check text-success me-1"></i> Sin restricciones</span> @endif
                                                                                    </div>
                                                                                    @if(!empty($pOp['observacion_fase']))
                                                                                        <div class="bg-light rounded p-2 text-muted fst-italic border mt-2 text-wrap text-break w-100" style="font-size: 0.7rem; word-wrap: break-word;">
                                                                                            <i class="fas fa-info-circle text-info me-1"></i> {{ $pOp['observacion_fase'] }}
                                                                                        </div>
                                                                                    @endif
                                                                                </div>

                                                                                {{-- COLUMNA 3: DETALLES Y ESTADO --}}
                                                                                <div class="col-12 col-md-4 p-0 ps-md-3 mt-3 mt-md-0">
                                                                                    <span class="d-block text-muted mb-1" style="font-size: 0.60rem; text-transform: uppercase;">Auditoría y Excepción</span>
                                                                                    <div class="d-flex flex-column gap-1 mb-2" style="font-size: 0.75rem;">
                                                                                        <span class="text-dark"><i class="fas fa-user-circle text-info me-1"></i> <strong>Responsable:</strong> {{ $conf->usuario->name ?? 'N/A' }}</span>

                                                                                        @php
                                                                                            $vencidoOp = $conf->vigente_hasta && \Carbon\Carbon::parse($conf->vigente_hasta)->isPast();
                                                                                        @endphp

                                                                                        <span class="text-dark"><i class="fas fa-calendar-check text-info me-1"></i> <strong>Vigente Hasta:</strong>
                                                                                            @if($conf->vigente_hasta)
                                                                                                <span class="{{ $vencidoOp ? 'text-danger fw-bold' : '' }}">{{ \Carbon\Carbon::parse($conf->vigente_hasta)->format('d/m/Y') }}</span>
                                                                                            @else
                                                                                                <span class="text-muted fst-italic">Indefinido</span>
                                                                                            @endif
                                                                                        </span>

                                                                                        @if($conf->justificacion)
                                                                                            <span class="text-dark mt-1 d-block lh-sm"><i class="fas fa-comment-dots text-info me-1"></i> <strong>Justificación:</strong> <br><span class="text-muted fst-italic">{{ $conf->justificacion }}</span></span>
                                                                                        @endif
                                                                                    </div>

                                                                                    {{-- SWITCH PARA CAMBIAR ESTADO --}}
                                                                                    <div class="mt-2 pt-2 border-top">
                                                                                        <form action="{{ route('certificados.operaciones.config.toggle', $conf->id) }}" method="POST" onclick="event.stopPropagation();">
                                                                                            @csrf
                                                                                            @method('PATCH')
                                                                                            <div class="form-check form-switch d-flex align-items-center gap-2 m-0 p-0">
                                                                                                <input class="form-check-input m-0" type="checkbox" role="switch" id="switchOp{{ $conf->id }}" onchange="this.form.submit()" {{ $conf->estado_activo ? 'checked' : '' }} style="cursor: pointer;">
                                                                                                <label class="form-check-label mb-0 {{ $conf->estado_activo ? 'text-success fw-bold' : 'text-danger fw-bold' }}" for="switchOp{{ $conf->id }}" style="font-size: 0.75rem; cursor: pointer;">
                                                                                                    {{ $conf->estado_activo ? 'Excepción Activa' : 'Excepción Inactiva' }}
                                                                                                </label>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed mt-2">
                                        <i class="fas fa-cogs fs-1 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-bold text-dark">Configuración Estándar</h6>
                                        <p class="mb-0 fs-7">No hay parámetros excepcionales configurados para esta operación.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 1: LÍNEAS --}}
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
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <i class="fas fa-layer-group text-primary"></i>
                                                                            <span class="text-dark">{{ $nombreLinea }}</span>
                                                                            <span class="badge bg-pastel-info text-info border border-info border-opacity-25 ms-1" style="font-size: 0.7rem; font-weight: 600; letter-spacing: 0.3px;">
                                                                                <i class="fas fa-database me-1 opacity-75"></i> Historial de Siasoft (Solo consulta)
                                                                            </span>
                                                                        </div>
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
                                    <!-- Botón reubicado -->
                                    <button type="button" class="btn bg-pastel-info shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalAlerta">
                                        <i class="fas fa-bell me-2 opacity-75"></i> Nueva Alerta
                                    </button>
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

                            {{-- TAB 3: HISTORIAL ETL / ESTADO --}}
                            <div class="tab-pane fade" id="historial" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-exchange-alt me-2"></i> Transiciones de Estado</h6>
                                            <!-- Botón reubicado -->
                                            <button type="button" class="btn bg-pastel-warning shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalTransicionar">
                                                <i class="fas fa-exchange-alt me-2 opacity-75"></i> Cambiar Estado
                                            </button>
                                        </div>

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
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-file-pdf me-2"></i> Gestión de Certificados</h6>
                                    <!-- Botón reubicado -->
                                    <button type="button" class="btn bg-pastel-danger shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalTipo">
                                        <i class="fas fa-file-pdf me-2 opacity-75"></i> Generar Certificado
                                    </button>
                                </div>
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
                                                                                    <a href="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $tipo->id, 'hash' => $version->hash_certificado]) }}"
                                                                                        target="_blank"
                                                                                        class="btn btn-sm btn-outline-danger rounded-1 px-2 shadow-sm"
                                                                                        title="Ver PDF Antiguo"
                                                                                        onclick="bloquearBotonUI(this)">
                                                                                        <i class="fas fa-file-pdf"></i>
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
                                                            {{-- BOTÓN REPARADO UTILIZANDO JS CENTRAL --}}
                                                            <button type="button" class="btn btn-sm btn-success rounded-1 px-4 fw-bold shadow-sm" onclick="enviarFormularioRemoto('formEditor_{{ $certId }}', this)">
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
        // 1. BLOQUEO GLOBAL DE FORMULARIOS: Impide por completo cualquier doble POST en toda la vista
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    // Si ya se está enviando, bloqueamos la ejecución extra
                    if (form.classList.contains('form-is-submitting')) {
                        e.preventDefault();
                        return;
                    }
                    form.classList.add('form-is-submitting');

                    // Buscamos el botón "submit" para deshabilitarlo visualmente
                    const submitBtn = form.querySelector('[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
                    }

                    // Acciones visuales exclusivas para el generador de PDF
                    if (form.id === 'formGenerarCertificado') {
                        document.getElementById('btnCancelCertificado').classList.add('d-none');
                        document.getElementById('loadingCertificado').classList.remove('d-none');
                    }
                });
            });

            // =========================================================================
            // LÓGICA DE MEMORIA DE PESTAÑAS (Para volver a la última pestaña abierta)
            // =========================================================================
            const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
            // Usamos un ID único combinando con el de la operación para evitar cruces
            const sessionKey = 'activeTab_Operacion_{{ $operacion->id }}';
            const activeTabId = sessionStorage.getItem(sessionKey);

            if (activeTabId) {
                const targetTab = document.querySelector(`button[data-bs-target="${activeTabId}"]`);
                if (targetTab) {
                    // Usando la forma nativa de Bootstrap para cambiar la pestaña activa en JS
                    targetTab.click();
                }
            }

            // Escuchar cambios para guardar en sesión
            tabButtons.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const targetId = event.target.getAttribute('data-bs-target');
                    sessionStorage.setItem(sessionKey, targetId);
                });
            });
        });

        // 2. FUNCIÓN DE ENVÍO REMOTO: Para modales aislados que envían formularios padres
        function enviarFormularioRemoto(formId, btn) {
            const form = document.getElementById(formId);
            if (!form || form.classList.contains('form-is-submitting')) return;

            form.classList.add('form-is-submitting');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Guardando...';

            // Ocultar el botón cancelar del modal
            const btnCancel = btn.previousElementSibling;
            if (btnCancel) btnCancel.style.display = 'none';

            form.submit();
        }

        // 3. BLOQUEO DE ENLACES EXTERNOS (PDF/Reportes): Evita múltiples peticiones GET simultáneas
        function bloquearBotonUI(btn, loadingText = '') {
            if (btn.classList.contains('form-is-submitting')) return false;

            const originalContent = btn.innerHTML;
            btn.classList.add('form-is-submitting');
            btn.style.pointerEvents = 'none';
            btn.innerHTML = `<i class='fas fa-spinner fa-spin ${loadingText ? "me-2" : ""}'></i> ${loadingText}`;

            // Reactivamos el botón después de 3 segundos (tiempo estimado de generación)
            setTimeout(() => {
                btn.classList.remove('form-is-submitting');
                btn.style.pointerEvents = 'auto';
                btn.innerHTML = originalContent;
            }, 3000);
            return true;
        }

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
    </script>

    <!-- LIBRERÍA CHART.JS -->
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
