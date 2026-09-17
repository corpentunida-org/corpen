<x-base-layout>
    <!-- ========================================== -->
    <!-- ESTILOS LOCALES PERSONALIZADOS             -->
    <!-- ========================================== -->
    <style>
        /* Colores pastel personalizados para badges y fondos */
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-secondary { background-color: #f1f5f9 !important; color: #475569 !important; border: none; }
        .bg-pastel-danger { background-color: #fee2e2 !important; color: #ef4444 !important; border: none; }

        /* Utilidades de tarjetas y tabs */
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .nav-tabs-custom .nav-link { border: none; color: #616161; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .nav-tabs-custom .nav-link.active { color: #0052cc; background: transparent; border-bottom: 3px solid #0052cc; }

        /* Tipografía y bordes auxiliares */
        .fs-7 { font-size: 0.9rem; }
        .fs-8 { font-size: 0.8rem; }
        .border-dashed { border-style: dashed !important; border-width: 2px !important; }
        .hover-opacity:hover { opacity: 0.8; transition: opacity 0.2s; }

        /* Acordeones y Tablas */
        .accordion-custom .accordion-button:not(.collapsed) { background-color: #f8fafc; color: #0f172a; box-shadow: none; }
        .accordion-custom .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,0.1); }
        .accordion-custom .accordion-button::after { background-size: 1.25rem; transition: all 0.3s ease; }
        .accordion-custom .accordion-item { border: 1px solid #e2e8f0; transition: all 0.3s ease; }
        .accordion-custom .accordion-item:hover { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border-color: #cbd5e1; }
        .table-inner th { font-weight: 600; color: #64748b; font-size: 0.75rem; background-color: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px;}

        /* Editor tipo Spreadsheet (Certificados) */
        .cuota-badge { width: 38px; height: 38px; font-size: 1rem; background: linear-gradient(135deg, #4a90e2, #0052cc); color: white; box-shadow: 0 4px 6px rgba(0, 82, 204, 0.2); }
        .table-spreadsheet { border-collapse: collapse; }
        .table-spreadsheet th { font-size: 0.75rem; text-transform: uppercase; color: #64748b; border: 1px solid #e2e8f0; padding: 1rem 0.75rem; background-color: #f8fafc; }
        .table-spreadsheet td { padding: 0; border: 1px solid #e2e8f0; vertical-align: middle; }
        .input-spreadsheet { width: 100%; border: none; padding: 0.85rem 0.75rem; background: transparent; outline: none; font-size: 0.85rem; color: #0f172a; transition: all 0.2s; }
        .input-spreadsheet:focus { background-color: #f0fdf4; box-shadow: inset 0 0 0 2px #22c55e; }
        select.input-spreadsheet { cursor: pointer; appearance: auto; -webkit-appearance: auto; padding-right: 2rem; }

        /* Animaciones para barras de progreso */
        .progress-minimalist { height: 6px; width: 100%; background-color: #fee2e2; border-radius: 4px; overflow: hidden; position: relative; }
        .progress-minimalist::before { content: ''; position: absolute; top: 0; left: -50%; width: 50%; height: 100%; background-color: #ef4444; animation: progress-slide 1.5s infinite ease-in-out; border-radius: 4px; }
        @keyframes progress-slide { 0% { left: -50%; width: 30%; } 50% { width: 60%; } 100% { left: 100%; width: 30%; } }
    </style>

    <div class="app-container py-4">

        {{-- ======================================================= --}}
        {{-- ENCABEZADO DE LA VISTA: OPERACIÓN                       --}}
        {{-- ======================================================= --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 px-2">
            <div>
                {{-- Enlace Volver Minimalista --}}
                <a href="{{ route('certificados.operaciones.index') }}"
                class="text-decoration-none text-muted d-inline-flex align-items-center mb-2"
                style="font-size: 0.75rem; transition: color 0.15s ease;"
                onmouseover="this.style.color='#0284c7';"
                onmouseout="this.style.color='#64748b';">
                    <div class="rounded-circle d-flex justify-content-center align-items-center me-1.5 border" style="width: 22px; height: 22px; background-color: #f8fafc; border-color: #e2e8f0 !important; color: #64748b;">
                        <i class="fas fa-chevron-left" style="font-size: 0.55rem;"></i>
                    </div>
                    Volver a la matriz
                </a>

                {{-- Título y Radicado Integrados --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="fw-medium text-secondary m-0" style="font-size: 1.15rem; letter-spacing: -0.3px;">Operación</h1>
                    <span class="badge rounded-pill d-inline-flex align-items-center shadow-none" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; padding: 0.25rem 0.7rem; font-size: 0.75rem; font-weight: 500; letter-spacing: -0.2px;">
                        # {{ $operacion->numero_radicado }}
                    </span>
                </div>
            </div>
        </div>
        <!-- ========================================== -->
        <!-- ALERTAS DE SESIÓN (Éxito / Error)          -->
        <!-- ========================================== -->
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

        <!-- ========================================== -->
        <!-- CONTENEDOR PRINCIPAL (GRID)                -->
        <!-- ========================================== -->
        <div class="row g-4">

            {{-- ======================================================= --}}
            {{-- COLUMNA IZQUIERDA (Info Básica, Técnica y Trazabilidad) --}}
            {{-- ======================================================= --}}
            <div class="col-xl-4 col-lg-5">

                {{-- TARJETA: INFORMACIÓN DEL CLIENTE (Tercero Maestras) --}}
                <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 10px; border: 1px solid #f1f5f9 !important;">
                    <div class="card-body p-3">

                        {{-- Encabezado de la Tarjeta --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-none" style="width: 36px; height: 36px; background-color: #e0f2fe; color: #0284c7;">
                                    <i class="fas fa-user-tie" style="font-size: 0.9rem;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold text-dark mb-0" style="font-size: 0.85rem; letter-spacing: -0.2px;">Datos del Cliente</h6>
                                    <span class="text-muted" style="font-size: 0.68rem;">Información de Maestras</span>
                                </div>
                            </div>
                            @if($operacion->tercero)
                                <button type="button" class="btn btn-light border rounded-pill px-2.5 py-0 text-muted shadow-none d-inline-flex align-items-center" style="font-size: 0.68rem; height: 26px; transition: all 0.2s ease; background-color: #f8fafc;" onmouseover="this.style.backgroundColor='#e0f2fe'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;" onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;" data-bs-toggle="modal" data-bs-target="#modalEditarTercero">
                                    <i class="fas fa-edit me-1 text-primary"></i> Editar
                                </button>
                            @endif
                        </div>

                        @if($operacion->tercero)
                            {{-- Contenedor Principal del Cliente (Tono Pastel Suave) --}}
                            <div class="rounded-3 p-2.5 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="overflow-hidden pe-2">
                                        <div class="fw-semibold text-dark text-truncate" style="font-size: 0.8rem;" title="{{ $operacion->tercero->nom_ter }}">{{ $operacion->tercero->nom_ter }}</div>
                                        <div class="text-muted font-monospace mt-0.5" style="font-size: 0.68rem;">NIT: {{ $operacion->tercero->cod_ter }}</div>
                                    </div>

                                    {{-- Píldora Desplegable de Historial --}}
                                    <div class="dropdown flex-shrink-0">
                                        <button class="btn btn-sm bg-white text-secondary rounded-pill border py-0.5 px-2 shadow-none d-flex align-items-center dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                style="font-size: 0.68rem; border-color: #cbd5e1 !important;"
                                                title="Ver historial de operaciones">
                                            <i class="fas fa-history text-muted opacity-50 me-1"></i>
                                            <span class="fw-semibold">{{ isset($operacionesDelTercero) ? $operacionesDelTercero->count() : 0 }}</span>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-1 mt-1" style="width: 240px; max-height: 220px; overflow-y: auto; font-size: 0.72rem;">
                                            <li><h6 class="dropdown-header text-muted text-uppercase fw-semibold mb-1" style="font-size: 0.6rem; letter-spacing: 0.3px;">Historial Reciente</h6></li>

                                            @if(isset($operacionesDelTercero) && $operacionesDelTercero->count() > 0)
                                                @foreach($operacionesDelTercero as $opTercero)
                                                    @php $esActual = $opTercero->id == $operacion->id; @endphp
                                                    <li>
                                                        <a class="dropdown-item rounded-2 py-1.5 px-2 mb-0.5 d-flex justify-content-between align-items-center {{ $esActual ? 'fw-semibold text-primary' : 'text-secondary' }}"
                                                        style="font-size: 0.7rem; background-color: {{ $esActual ? '#e0f2fe' : 'transparent' }};"
                                                        href="{{ route('certificados.operaciones.show', $opTercero->id) }}">
                                                            <span class="text-truncate" style="max-width: 130px;">
                                                                <i class="fas {{ $esActual ? 'fa-dot-circle text-primary' : 'fa-circle text-muted opacity-25' }} me-1" style="font-size: 0.5rem;"></i>
                                                                Rad: {{ $opTercero->numero_radicado }}
                                                            </span>
                                                            <span class="badge rounded-pill border {{ $esActual ? 'bg-primary text-white border-primary' : 'bg-white text-muted border-light' }}" style="font-size: 0.58rem; font-weight: 400; padding: 0.15rem 0.4rem;">
                                                                API-{{ str_pad($opTercero->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li><span class="dropdown-item text-muted py-1" style="font-size: 0.68rem;">Sin operaciones previas</span></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Datos de Contacto (Lista Minimalista) --}}
                            <div class="d-flex flex-column gap-2" style="font-size: 0.72rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted d-flex align-items-center gap-1.5"><i class="fas fa-phone-alt opacity-50" style="font-size: 0.65rem;"></i> Teléfono:</span>
                                    <span class="fw-semibold text-dark">{{ $operacion->tercero->tel ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted d-flex align-items-center gap-1.5"><i class="fas fa-envelope opacity-50" style="font-size: 0.65rem;"></i> Email:</span>
                                    <span class="fw-semibold text-dark text-truncate ms-2" style="max-width: 150px;" title="{{ $operacion->tercero->email }}">{{ $operacion->tercero->email ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted d-flex align-items-center gap-1.5"><i class="fas fa-map-marker-alt opacity-50" style="font-size: 0.65rem;"></i> Ciudad:</span>
                                    <span class="fw-semibold text-dark">{{ $operacion->tercero->ciudad ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted d-flex align-items-center gap-1.5"><i class="fas fa-map opacity-50" style="font-size: 0.65rem;"></i> Dirección:</span>
                                    <span class="fw-semibold text-dark text-end text-truncate ms-2" style="max-width: 160px;" title="{{ $operacion->tercero->dir }}">{{ $operacion->tercero->dir ?? 'N/A' }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 rounded" style="background-color: #fefce8; border: 1px dashed #fde047; color: #854d0e;">
                                <span style="font-size: 0.72rem;">Tercero no encontrado en maestras.</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- MODAL: EDITAR DATOS DEL TERCERO --}}
                @if($operacion->tercero)
                <div class="modal fade" id="modalEditarTercero" tabindex="-1" aria-labelledby="modalEditarTerceroLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                            <div class="modal-header border-bottom px-4 py-3" style="background-color: #f8fafc; border-color: #f1f5f9 !important;">
                                <h5 class="modal-title fw-semibold text-dark d-flex align-items-center gap-2" id="modalEditarTerceroLabel" style="font-size: 0.95rem;">
                                    <i class="fas fa-user-edit text-primary"></i> Actualizar Datos del Cliente
                                </h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
                            </div>
                            <form action="{{ route('certificados.operaciones.actualizar_tercero', $operacion->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body p-4" style="font-size: 0.75rem;">
                                    <div class="p-2.5 rounded-3 mb-3 text-muted d-flex align-items-center gap-2" style="background-color: #e0f2fe; border: 1px solid #bae6fd; color: #0369a1 !important;">
                                        <i class="fas fa-info-circle flex-shrink-0"></i>
                                        <span>El "Nombre Completo" se autogenerará al guardar uniendo los nombres y apellidos en MAYÚSCULAS.</span>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Primer Nombre</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="text-transform: uppercase; font-size: 0.75rem; border-color: #cbd5e1;" name="nom1" value="{{ $operacion->tercero->nom1 }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Segundo Nombre</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="text-transform: uppercase; font-size: 0.75rem; border-color: #cbd5e1;" name="nom2" value="{{ $operacion->tercero->nom2 }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Primer Apellido</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="text-transform: uppercase; font-size: 0.75rem; border-color: #cbd5e1;" name="apl1" value="{{ $operacion->tercero->apl1 }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Segundo Apellido</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="text-transform: uppercase; font-size: 0.75rem; border-color: #cbd5e1;" name="apl2" value="{{ $operacion->tercero->apl2 }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Teléfono Principal (tel)</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="font-size: 0.75rem; border-color: #cbd5e1;" name="tel" value="{{ $operacion->tercero->tel }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Teléfono Secundario (tel1)</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="font-size: 0.75rem; border-color: #cbd5e1;" name="tel1" value="{{ $operacion->tercero->tel1 }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Correo Electrónico</label>
                                            <input type="email" class="form-control form-control-sm shadow-none" style="font-size: 0.75rem; border-color: #cbd5e1;" name="email" value="{{ $operacion->tercero->email }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Dirección</label>
                                            <input type="text" class="form-control form-control-sm shadow-none" style="font-size: 0.75rem; border-color: #cbd5e1;" name="dir" value="{{ $operacion->tercero->dir }}">
                                        </div>
                                    </div>

                                    @php
                                        $idDist = trim((string)($operacion->tercero->cod_dist ?? ''));
                                        $nomDist = '';
                                        foreach($distritos as $d) {
                                            if (trim((string)($d->COD_DIST ?? $d->cod_dist ?? '')) === $idDist) {
                                                $nomDist = $d->NOM_DIST ?? $d->nom_dist ?? $d->DETALLE ?? $d->COMPUEST ?? 'Sin nombre';
                                                break;
                                            }
                                        }

                                        $idTipo = trim((string)($operacion->tercero->tip_prv ?? ''));
                                        $nomTipo = '';
                                        foreach($maeTipos as $t) {
                                            if (trim((string)($t->id ?? $t->tip_prv ?? '')) === $idTipo) {
                                                $nomTipo = $t->nombre ?? $t->descripcion ?? $t->tipo ?? 'Sin descripción';
                                                break;
                                            }
                                        }

                                        $idCong = trim((string)($operacion->tercero->congrega ?? ''));
                                        $nomCong = '';
                                        foreach($congregaciones as $c) {
                                            if (trim((string)($c->codigo ?? $c->congrega ?? '')) === $idCong) {
                                                $nomCong = $c->nombre ?? $c->descripcion ?? $c->iglesia ?? 'Sin descripción';
                                                break;
                                            }
                                        }
                                    @endphp

                                    {{-- Resumen y Botón Desplegable Actualizar --}}
                                    <div class="p-2.5 rounded-3 mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                        <div class="text-muted" style="font-size: 0.68rem;">
                                            <strong>Distrito:</strong> {{ $idDist !== '' ? ($nomDist !== '' ? "$idDist - $nomDist" : $idDist) : 'Sin asignación' }} <span class="mx-1">|</span>
                                            <strong>Tipo:</strong> {{ $idTipo !== '' ? ($nomTipo !== '' ? "$idTipo - $nomTipo" : $idTipo) : 'Sin asignación' }} <span class="mx-1">|</span>
                                            <strong>Congregación:</strong> {{ $idCong !== '' ? ($nomCong !== '' ? "$idCong - $nomCong" : $idCong) : 'Sin asignación' }}
                                        </div>
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-primary fw-semibold shadow-none flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#camposActualizar" aria-expanded="false" aria-controls="camposActualizar" style="font-size: 0.68rem; background-color: #e0f2fe; border-color: #bae6fd !important;">
                                            Configurar Selects
                                        </button>
                                    </div>

                                    {{-- Contenedor Desplegable con los Selects --}}
                                    <div class="collapse" id="camposActualizar">
                                        <div class="row g-3 p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                            <div class="col-md-4">
                                                <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Distrito</label>
                                                <select class="form-select form-select-sm select2-buscador shadow-none" name="cod_dist" id="select_cod_dist" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                    <option value="">Seleccione o busque...</option>
                                                    @foreach($distritos as $distritoItem)
                                                        @php $valDistrito = trim($distritoItem->COD_DIST ?? $distritoItem->cod_dist ?? ''); @endphp
                                                        <option value="{{ $valDistrito }}" {{ $idDist === (string)$valDistrito ? 'selected' : '' }}>
                                                            {{ $valDistrito }} - {{ $distritoItem->NOM_DIST ?? $distritoItem->nom_dist ?? $distritoItem->DETALLE ?? $distritoItem->COMPUEST ?? 'Sin nombre' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Tipo</label>
                                                <select class="form-select form-select-sm select2-buscador shadow-none" name="tip_prv" id="select_tip_prv" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                    <option value="">Seleccione o busque...</option>
                                                    @foreach($maeTipos as $tipoItem)
                                                        @php $valTipo = trim($tipoItem->id ?? $tipoItem->tip_prv ?? ''); @endphp
                                                        <option value="{{ $valTipo }}" {{ $idTipo === (string)$valTipo ? 'selected' : '' }}>
                                                            {{ $tipoItem->nombre ?? $tipoItem->descripcion ?? $tipoItem->tipo ?? 'Sin descripción' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label text-muted mb-1" style="font-size: 0.68rem;">Congregación</label>
                                                <select class="form-select form-select-sm select2-buscador shadow-none" name="congrega" id="select_congrega" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                    <option value="">Seleccione o busque...</option>
                                                    @foreach($congregaciones as $congregaItem)
                                                        @php $valCongrega = trim($congregaItem->codigo ?? $congregaItem->congrega ?? ''); @endphp
                                                        <option value="{{ $valCongrega }}" {{ $idCong === (string)$valCongrega ? 'selected' : '' }}>
                                                            {{ $valCongrega }} - {{ $congregaItem->nombre ?? $congregaItem->descripcion ?? $congregaItem->iglesia ?? 'Sin descripción' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer border-top px-4 py-3" style="background-color: #f8fafc; border-color: #f1f5f9 !important;">
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 text-muted shadow-none" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancelar</button>
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-3 text-primary fw-semibold shadow-none" style="font-size: 0.7rem; background-color: #e0f2fe; border-color: #bae6fd !important;">
                                        <i class="fas fa-save me-1"></i> Actualizar y Concatenar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- TARJETA: INFORMACIÓN TÉCNICA Y ETL --}}
                <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 10px; border: 1px solid #f1f5f9 !important;">
                    <div class="card-body p-3">
                        <h6 class="text-uppercase text-secondary mb-3 fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.3px;">
                            <i class="fas fa-microchip me-1.5 text-primary"></i> Info Técnica (ETL)
                        </h6>
                        <div class="d-flex flex-column gap-2" style="font-size: 0.72rem;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Número Radicado:</span>
                                <span class="font-monospace fw-semibold text-dark">{{ $operacion->numero_radicado }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Lote / Bloque:</span>
                                <span class="badge rounded-pill" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.58rem; font-weight: 500; padding: 0.2rem 0.6rem;">
                                    API-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Fecha Creación Lote:</span>
                                <span class="fw-semibold text-dark">
                                    {{ $operacion->created_at ? $operacion->created_at->format('d/m/Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TARJETA: MATRIZ DE TRAZABILIDAD (TIMELINE DE AUDITORÍA) --}}
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 10px; border: 1px solid #f1f5f9 !important; height: 460px; max-height: 460px; display: flex; flex-direction: column; overflow: hidden !important;">

                    {{-- Header Trazabilidad Estático (Altura fija de 60px) --}}
                    <div class="px-3 pt-3 pb-2 border-bottom flex-shrink-0" style="background-color: #f8fafc; border-color: #f1f5f9 !important; height: 60px;">
                        <h6 class="fw-semibold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.85rem; letter-spacing: -0.2px;">
                            <i class="fas fa-shield-alt text-muted opacity-75"></i> Matriz de Trazabilidad
                        </h6>
                        <span class="text-muted" style="font-size: 0.65rem;">Registro de eventos y auditoría del sistema</span>
                    </div>

                    {{-- Cuerpo con Scroll Forzado por CSS Inline (Sin depender de clases externas) --}}
                    <div class="p-3" style="flex: 1 1 auto !important; overflow-y: scroll !important; min-height: 0 !important; height: calc(460px - 60px) !important;">
                        @if(isset($logsAuditoria) && $logsAuditoria->count() > 0)
                            <div class="position-relative ms-2" style="border-left: 2px solid #e2e8f0;">
                                @foreach($logsAuditoria as $log)
                                    @if(is_null($log->id_car_sia_operaciones) || $log->id_car_sia_operaciones == $operacion->id)
                                        <div class="position-relative mb-3 ps-3">
                                            {{-- Punto del Timeline --}}
                                            <span class="position-absolute rounded-circle border border-2 border-white shadow-none" style="width: 10px; height: 10px; left: -6px; top: 4px; background-color: #0284c7;"></span>

                                            {{-- Tarjeta del Log Minimalista con Tonos Pastel --}}
                                            <div class="p-2.5 rounded-3 border bg-white" style="border-color: #e2e8f0 !important; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                                <div class="d-flex justify-content-between align-items-start mb-1 gap-2">
                                                    <span class="fw-semibold text-dark text-truncate" style="font-size: 0.72rem; line-height: 1.2; max-width: 150px;" title="{{ $log->tituloEvento ?? 'Evento de Sistema' }}">
                                                        {{ $log->tituloEvento ?? 'Evento de Sistema' }}
                                                    </span>
                                                    <span class="badge rounded-pill text-muted bg-light border flex-shrink-0" style="font-size: 0.58rem; padding: 0.15rem 0.4rem; font-weight: normal;">
                                                        {{ $log->fechaEvento ?? '—' }}
                                                    </span>
                                                </div>

                                                {{-- Info Usuario --}}
                                                <div class="text-muted mb-2 d-flex justify-content-between align-items-center gap-1" style="font-size: 0.65rem;">
                                                    <div class="text-truncate pe-1">
                                                        <i class="fas fa-user-circle opacity-50 me-1"></i>
                                                        <span class="fw-semibold text-dark">{{ $log->nombreUsuario ?? 'Sistema Automático' }}</span>
                                                        @if(!empty($log->cargoUsuario))
                                                            <span class="text-muted opacity-75">({{ $log->cargoUsuario }})</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($log->ipDelUsuario))
                                                        <span class="text-muted font-monospace flex-shrink-0 bg-light px-1.5 py-0.5 rounded border border-light" style="font-size: 0.58rem;" title="IP de origen">
                                                            {{ $log->ipDelUsuario }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center pt-2 border-top gap-1 flex-wrap" style="border-color: #f1f5f9 !important;">
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #475569; border-color: #cbd5e1 !important; font-size: 0.58rem; font-weight: 400; padding: 0.15rem 0.4rem;">
                                                            <i class="fas fa-desktop me-0.5 opacity-50"></i> {{ $log->origenEvento ?? 'Sistema' }}
                                                        </span>

                                                        @if(is_null($log->id_car_sia_operaciones))
                                                            <span class="badge rounded-pill" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 0.58rem; font-weight: 500; padding: 0.15rem 0.5rem;" title="Evento a nivel de Lote/Bloque">
                                                                <i class="fas fa-layer-group me-0.5"></i> Lote General
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill" style="background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-size: 0.58rem; font-weight: 500; padding: 0.15rem 0.5rem;" title="Acción directa en esta operación">
                                                                <i class="fas fa-user-check me-0.5"></i> Individual
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Botón Detalles --}}
                                                    @if($log->hayDetalles && isset($log->detalles_procesados) && count($log->detalles_procesados) > 0)
                                                        <button type="button" class="btn btn-sm text-primary p-0 m-0 border-0 bg-transparent fw-semibold d-flex align-items-center gap-1 shadow-none" style="font-size: 0.62rem;" onclick="document.getElementById('detalles-trazabilidad-{{ $loop->index }}').classList.toggle('d-none')">
                                                            <i class="fas fa-search-plus"></i> Detalles
                                                        </button>
                                                    @endif
                                                </div>

                                                {{-- Contenedor de Detalles Oculto --}}
                                                @if($log->hayDetalles && isset($log->detalles_procesados) && count($log->detalles_procesados) > 0)
                                                    <div id="detalles-trazabilidad-{{ $loop->index }}" class="d-none mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                                        <div class="p-2 rounded-2 text-wrap text-break" style="font-size: 0.62rem; background-color: #f8fafc; border: 1px solid #e2e8f0;">
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
                                                                    <div class="mb-1.5 pb-1 border-bottom border-light last-border-0">
                                                                        <strong class="text-primary text-uppercase d-block mb-0.5" style="font-size: 0.55rem; letter-spacing: 0.3px;">
                                                                            {{ str_replace('_', ' ', $llave) }}
                                                                        </strong>

                                                                        @if(is_array($datosParseados))
                                                                            <div class="d-flex flex-wrap gap-1">
                                                                                @foreach($datosParseados as $subKey => $subVal)
                                                                                    <span class="badge rounded-pill border" style="background-color: #ffffff; color: #334155; border-color: #cbd5e1 !important; font-size: 0.58rem; font-weight: normal; padding: 0.15rem 0.4rem;">
                                                                                        <span class="text-muted">{{ str_replace('_', ' ', ucfirst($subKey)) }}:</span>
                                                                                        <span class="fw-semibold">{{ is_array($subVal) ? json_encode($subVal, JSON_UNESCAPED_UNICODE) : $subVal }}</span>
                                                                                    </span>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <span class="text-dark fw-medium" style="font-size: 0.62rem;">
                                                                                {{ $datosParseados }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 text-muted h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-clipboard-list fs-3 mb-2 text-secondary opacity-25"></i>
                                <h6 class="fw-semibold text-dark mb-1" style="font-size: 0.8rem;">Sin Movimientos</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.68rem;">No hay registros de auditoría para mostrar.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- ---------------------------------------------------- -->
            <!-- COLUMNA DERECHA (Sistema de Pestañas / Navegación)   -->
            <!-- ---------------------------------------------------- -->
            <div class="col-xl-8 col-lg-7">
                <div class="card card-custom shadow-sm border-0 h-100">

                    {{-- ESTILOS DE BARRA Y PESTAÑAS MINIMALISTAS --}}
                    <style>
                        .tabs-scrollable {
                            scrollbar-width: thin;
                            scrollbar-color: #e2e8f0 transparent;
                        }
                        .tabs-scrollable::-webkit-scrollbar {
                            height: 4px;
                        }
                        .tabs-scrollable::-webkit-scrollbar-track {
                            background: transparent;
                        }
                        .tabs-scrollable::-webkit-scrollbar-thumb {
                            background-color: #cbd5e1;
                            border-radius: 10px;
                        }
                        .tabs-scrollable::-webkit-scrollbar-thumb:hover {
                            background-color: #94a3b8;
                        }

                        .nav-tabs-custom .nav-link {
                            border: none !important;
                            color: #64748b;
                            font-size: 0.76rem;
                            font-weight: 500;
                            padding: 0.5rem 0.75rem;
                            border-radius: 8px;
                            transition: all 0.2s ease;
                            background-color: transparent;
                            white-space: nowrap;
                        }
                        .nav-tabs-custom .nav-link:hover {
                            color: #0f172a;
                            background-color: #f8fafc;
                        }
                        .nav-tabs-custom .nav-link.active {
                            color: #0f172a !important;
                            background-color: #f1f5f9 !important;
                            font-weight: 600;
                        }
                    </style>

                    {{-- CABECERA: TABS DE NAVEGACIÓN --}}
                    <div class="card-header bg-white pt-2.5 pb-2 border-bottom px-3 px-md-4" style="border-radius: 16px 16px 0 0; border-color: #eaeeed !important;">
                        <ul class="nav nav-tabs nav-tabs-custom border-0 d-flex flex-nowrap overflow-auto tabs-scrollable gap-1 align-items-center" id="operacionTabs" role="tablist">

                            {{-- Líneas --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active d-flex align-items-center" id="lineas-tab" data-bs-toggle="tab" data-bs-target="#lineas" type="button" role="tab">
                                    <span class="bg-pastel-primary text-primary rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-sitemap"></i>
                                    </span>
                                    Líneas
                                </button>
                            </li>

                            {{-- Certificados --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="certificados-tab" data-bs-toggle="tab" data-bs-target="#certificados" type="button" role="tab">
                                    <span class="bg-pastel-danger text-danger rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-file-pdf"></i>
                                    </span>
                                    Certificados
                                </button>
                            </li>

                            {{-- Documentos --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                                    <span class="bg-pastel-primary text-primary rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-folder-open"></i>
                                    </span>
                                    Documentos
                                </button>
                            </li>

                            {{-- Soportes --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="soportes-tab" data-bs-toggle="tab" data-bs-target="#soportes" type="button" role="tab">
                                    <span class="bg-pastel-secondary text-secondary rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-paperclip"></i>
                                    </span>
                                    Soportes
                                </button>
                            </li>

                            {{-- Interacciones --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="interacciones-tab" data-bs-toggle="tab" data-bs-target="#interacciones" type="button" role="tab">
                                    <span class="bg-pastel-warning text-warning rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-headset"></i>
                                    </span>
                                    Interacciones
                                </button>
                            </li>

                            {{-- Alertas --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="alertas-tab" data-bs-toggle="tab" data-bs-target="#alertas" type="button" role="tab">
                                    <span class="bg-pastel-warning text-warning rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-bell"></i>
                                    </span>
                                    Alertas
                                </button>
                            </li>

                            {{-- Historial --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab">
                                    <span class="bg-pastel-secondary text-secondary rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-history"></i>
                                    </span>
                                    Historial
                                </button>
                            </li>

                            {{-- Gráficos --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="graficos-tab" data-bs-toggle="tab" data-bs-target="#graficos" type="button" role="tab">
                                    <span class="bg-pastel-info text-info rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-chart-pie"></i>
                                    </span>
                                    Gráficos
                                </button>
                            </li>

                            {{-- Parámetros --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="parametros-tab" data-bs-toggle="tab" data-bs-target="#parametros" type="button" role="tab">
                                    <span class="bg-pastel-secondary text-dark rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    Parámetros
                                </button>
                            </li>

                            {{-- Operarios --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center" id="operarios-tab" data-bs-toggle="tab" data-bs-target="#operarios" type="button" role="tab">
                                    <span class="bg-pastel-success text-success rounded-circle d-inline-flex justify-content-center align-items-center me-1.5 flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                        <i class="fas fa-users-cog"></i>
                                    </span>
                                    Operarios
                                </button>
                            </li>

                        </ul>
                    </div>

                    {{-- CUERPO DE TABS --}}
                    <div class="card-body p-4">
                        <div class="tab-content" id="operacionTabsContent">

                            {{-- ======================================================= --}}
                            {{-- TAB 0: PARÁMETROS (Configuraciones de excepción)        --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="parametros" role="tabpanel">

                                {{-- ENCABEZADO Y BOTÓN --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Configuración General</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Configuraciones de excepción y parámetros de la operación.</p>
                                    </div>
                                    <button type="button"
                                            class="btn btn-light border rounded-pill px-3 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center"
                                            style="font-size: 0.7rem; height: 26px; transition: all 0.2s ease;"
                                            onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;"
                                            onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;"
                                            onclick="$('#modalCrearConfiguracion').modal('show');">
                                        <i class="fas fa-plus-circle me-1.5 text-primary opacity-75"></i> Nuevos Parámetros
                                    </button>
                                </div>

                                @if(isset($operacionesConfiguradas) && $operacionesConfiguradas->count() > 0)

                                    {{-- CONTENEDOR TABLA ULTRA PRO --}}
                                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px; overflow: hidden; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">

                                        {{-- TOOLBAR PEQUEÑO --}}
                                        <div class="px-3 py-2 border-bottom text-muted d-flex justify-content-between align-items-center" style="font-size: 0.7rem; background-color: #f8fafc; border-color: #f1f5f9 !important;">
                                            <span><i class="fas fa-hand-pointer text-primary opacity-75 me-1"></i> Haz clic en la fila para ver detalles, trazabilidad y editar la lógica</span>
                                            <span class="badge rounded-pill shadow-sm" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; padding: 0.25rem 0.6rem; font-size: 0.65rem; font-weight: 500;">
                                                {{ collect($operacionesConfiguradas)->sum(fn($op) => collect($op->configuracion)->count()) }} Registros
                                            </span>
                                        </div>

                                        <div class="table-responsive custom-scrollbar" style="max-height: 500px; overflow-y: auto;">
                                            <table class="table table-sm align-middle mb-0 text-nowrap" style="font-size: 0.72rem; border-collapse: separate; border-spacing: 0;">

                                                <thead class="text-uppercase text-secondary sticky-top shadow-sm" style="font-size: 0.6rem; letter-spacing: 0.3px; background-color: #f8fafc; z-index: 1;">
                                                    <tr>
                                                        <th class="text-center py-3 ps-3 border-bottom fw-medium" style="width: 5%; border-color: #e2e8f0 !important;"></th>
                                                        <th class="text-center py-3 px-2 border-bottom fw-medium" style="width: 10%; border-color: #e2e8f0 !important;">Alcance</th>
                                                        <th class="py-3 px-3 border-bottom fw-medium text-start" style="width: 12%; border-color: #e2e8f0 !important;">Radicado</th>
                                                        <th class="py-3 px-3 border-bottom fw-medium text-start" style="width: 15%; border-color: #e2e8f0 !important;">Cliente</th>
                                                        <th class="py-3 px-3 border-bottom fw-medium text-start" style="width: 18%; border-color: #e2e8f0 !important;">Acción Vencimiento</th>
                                                        <th class="text-center py-3 px-2 border-bottom fw-medium" style="width: 10%; border-color: #e2e8f0 !important;">Frec.</th>
                                                        <th class="text-center py-3 px-2 border-bottom fw-medium" style="width: 10%; border-color: #e2e8f0 !important;">Est. Acción</th>
                                                        <th class="text-center py-3 px-2 border-bottom fw-medium" style="width: 8%; border-color: #e2e8f0 !important;">Notif.</th>
                                                        <th class="text-center py-3 pe-3 border-bottom fw-medium" style="width: 12%; border-color: #e2e8f0 !important;">Estado</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="border-top-0">
                                                    {{-- ITERACIÓN DE CONFIGURACIONES --}}
                                                    @foreach($operacionesConfiguradas as $op)
                                                        @php
                                                            $configs = $op->configuracion instanceof \Illuminate\Support\Collection
                                                                ? $op->configuracion
                                                                : collect([$op->configuracion])->filter();
                                                        @endphp

                                                        @foreach($configs as $conf)
                                                            @php
                                                                // Diferenciación Visual LOTE vs EXCEPCIÓN
                                                                $esLote = is_null($conf->id_car_sia_operaciones);
                                                                $tipoAlcance = $esLote ? 'LOTE' : 'EXCEPCIÓN';
                                                                $badgeAlcance = $esLote ? 'background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;' : 'background-color: #f0fdf4; color: #059669; border: 1px solid #a7f3d0;';
                                                                $iconoAlcance = $esLote ? 'fa-layer-group' : 'fa-user-tag';

                                                                $configBaseOp = $conf->configuracionBase;
                                                                $accionVencOp = $configBaseOp?->accionVencimiento;
                                                                $nombreCliente = trim(($op->tercero?->nom_ter ?? 'Sin Tercero') . ' ' . ($op->tercero?->apl1 ?? ''));

                                                                // Parámetros JSON (Editables)
                                                                $pOp = is_array($conf->parametros) ? $conf->parametros : (json_decode($conf->parametros, true) ?? []);
                                                                $claseMoraOp = strtolower($pOp['clasificacion_mora'] ?? 'desconocido');
                                                            @endphp

                                                            {{-- FILA PRINCIPAL (VISIBLE) --}}
                                                            <tr style="background-color: #fff; cursor: {{ $esLote ? 'not-allowed' : 'pointer' }}; transition: background-color 0.15s ease;"
                                                                class="parent-row border-bottom {{ !$conf->estado_activo ? 'opacity-75' : '' }}"
                                                                style="border-color: #f1f5f9 !important;"

                                                                @if(!$esLote)
                                                                    onclick="toggleParametros('det-op-{{ $conf->id }}', this)"
                                                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                                                    onmouseout="this.style.backgroundColor='#ffffff'"
                                                                @endif>

                                                                <td class="text-center py-3 ps-3">
                                                                    @if($esLote)
                                                                        <i class="fas fa-lock text-muted opacity-25" title="La configuración en LOTE no es editable desde aquí"></i>
                                                                    @else
                                                                        <i class="fas fa-chevron-circle-down text-info opacity-50 icon-toggle" style="font-size: 0.8rem;"></i>
                                                                    @endif
                                                                </td>

                                                                {{-- COLUMNA: ALCANCE VISUAL --}}
                                                                <td class="text-center py-3 px-2">
                                                                    <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="{{ $badgeAlcance }} padding: 0.2rem 0.5rem; font-size: 0.6rem; font-weight: 400;">
                                                                        <i class="fas {{ $iconoAlcance }} opacity-75"></i> {{ $tipoAlcance }}
                                                                    </span>
                                                                </td>

                                                                <td class="font-monospace fw-medium text-secondary py-3 px-3">
                                                                    {{ $esLote ? 'API-'.str_pad($conf->numero_bloque, 4, '0', STR_PAD_LEFT) : ($op->numero_radicado ?? 'N/A') }}
                                                                </td>

                                                                <td class="text-truncate py-3 px-3 text-secondary" title="{{ $nombreCliente }}" style="max-width: 140px;">
                                                                    {{ $nombreCliente }}
                                                                </td>

                                                                <td class="fw-medium text-secondary text-truncate py-3 px-3" title="{{ $accionVencOp?->nombre ?? 'N/A' }}" style="max-width: 180px;">
                                                                    {{ $accionVencOp?->nombre ?? 'N/A' }}
                                                                </td>

                                                                <td class="text-center py-3 px-2">
                                                                    @if($configBaseOp?->frecuencia_recordatorio_dias)
                                                                        <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; font-size: 0.6rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                            Cada {{ $configBaseOp->frecuencia_recordatorio_dias }} d
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted opacity-50">—</span>
                                                                    @endif
                                                                </td>

                                                                <td class="text-center py-3 px-2">
                                                                    @if(isset($accionVencOp?->estado))
                                                                        <span class="badge rounded-pill border {{ $accionVencOp->estado ? 'bg-white text-success border-success' : 'bg-white text-danger border-danger' }}" style="font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                            {{ $accionVencOp->estado ? 'ACTIVA' : 'INACTIVA' }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted opacity-50">—</span>
                                                                    @endif
                                                                </td>

                                                                <td class="text-center py-3 px-2">
                                                                    @if($conf->estado_notificacion)
                                                                        <span class="text-success opacity-75"><i class="fas fa-bell"></i></span>
                                                                    @else
                                                                        <span class="text-muted opacity-25"><i class="fas fa-bell-slash"></i></span>
                                                                    @endif
                                                                </td>

                                                                <td class="text-center py-3 pe-3" onclick="event.stopPropagation();">
                                                                    @if(method_exists($conf, 'trashed') && $conf->trashed())
                                                                        <span class="badge rounded-pill bg-danger text-white" style="font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">ELIMINADA</span>
                                                                    @else
                                                                        <form action="{{ route('certificados.operaciones.config.toggle', $conf->id) }}" method="POST" class="m-0 p-0 d-inline">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <button type="submit"
                                                                                    class="badge rounded-pill border-0 {{ $conf->estado_activo ? 'bg-success' : 'bg-secondary' }} text-white shadow-none"
                                                                                    style="cursor: pointer; padding: 0.25rem 0.6rem; font-size: 0.6rem; font-weight: 500; transition: opacity 0.2s;"
                                                                                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                                                                                    title="Clic para cambiar a {{ $conf->estado_activo ? 'INACTIVO' : 'ACTIVO' }}">
                                                                                {{ $conf->estado_activo ? 'ACTIVA' : 'INACTIVA' }}
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>

                                                            {{-- Evitamos renderizar el formulario si es LOTE --}}
                                                            @if(!$esLote)
                                                                {{-- FILA DESPLEGABLE (FORMULARIO EDICIÓN JSON Y TRAZABILIDAD) --}}
                                                                <tr id="det-op-{{ $conf->id }}" style="display: none; background-color: #f8fafc;">
                                                                    <td colspan="9" class="p-0 border-0">
                                                                        <div class="p-3" onclick="event.stopPropagation();">

                                                                            <form action="{{ route('certificados.operaciones.config.update_parametros', $conf->id) }}" method="POST" class="bg-white rounded-3 shadow-sm border p-3 position-relative" style="border-color: #e2e8f0 !important; border-left: 3px solid #0284c7 !important;">
                                                                                @csrf
                                                                                @method('PUT')

                                                                                {{-- METADATOS Y TRAZABILIDAD COMPLETA --}}
                                                                                <div class="bg-light border rounded-3 p-2 mb-3 d-flex flex-wrap gap-3 align-items-center text-muted" style="font-size: 0.65rem; border-color: #f1f5f9 !important;">
                                                                                    <span><i class="fas fa-fingerprint opacity-50 me-1"></i><strong>ID Reg:</strong> {{ $conf->id }}</span>
                                                                                    <span><i class="fas fa-cubes opacity-50 me-1"></i><strong>Bloque:</strong> {{ $conf->numero_bloque }}</span>
                                                                                    <span><i class="fas fa-file-invoice opacity-50 me-1"></i><strong>ID Op:</strong> {{ $conf->id_car_sia_operaciones ?? 'N/A' }}</span>
                                                                                    <span><i class="fas fa-cog opacity-50 me-1"></i><strong>ID Config:</strong> {{ $conf->id_car_sia_config }}</span>
                                                                                    <span><i class="fas fa-user-circle opacity-50 me-1"></i><strong>Usuario Mod:</strong> {{ $conf->id_user }} ({{ $conf->usuario->name ?? 'Sistema' }})</span>
                                                                                    <span><i class="fas fa-calendar-plus opacity-50 me-1"></i><strong>Creado:</strong> {{ $conf->created_at ? $conf->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                                                                    <span><i class="fas fa-sync-alt opacity-50 me-1"></i><strong>Modificado:</strong> {{ $conf->updated_at ? $conf->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                                                                </div>

                                                                                <div class="row g-3">
                                                                                    {{-- COLUMNA 1: EDICIÓN DE PARÁMETROS JSON (Clasificación) --}}
                                                                                    <div class="col-md-4 border-end pe-md-3" style="border-color: #f1f5f9 !important;">
                                                                                        <span class="d-block fw-medium text-secondary mb-2" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.3px;"><i class="fas fa-sliders-h opacity-75 me-1 text-primary"></i> Lógica (Clasificación)</span>

                                                                                        <div class="mb-2">
                                                                                            <label class="form-label text-muted mb-1" style="font-size: 0.65rem;">Clasificación de Mora</label>
                                                                                            <select name="parametros[clasificacion_mora]" class="form-select form-select-sm shadow-none" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                                                                <option value="bueno" {{ $claseMoraOp == 'bueno' ? 'selected' : '' }}>BUENO</option>
                                                                                                <option value="regular" {{ $claseMoraOp == 'regular' ? 'selected' : '' }}>REGULAR</option>
                                                                                                <option value="atencion_especial" {{ $claseMoraOp == 'atencion_especial' ? 'selected' : '' }}>ATENCIÓN ESPECIAL</option>
                                                                                                <option value="restringido" {{ $claseMoraOp == 'restringido' ? 'selected' : '' }}>RESTRINGIDO</option>
                                                                                                <option value="irregular" {{ $claseMoraOp == 'irregular' ? 'selected' : '' }}>IRREGULAR</option>
                                                                                            </select>
                                                                                        </div>

                                                                                        <div class="row g-2 mb-2">
                                                                                            <div class="col-6">
                                                                                                <label class="form-label text-muted mb-1" style="font-size: 0.65rem;">Max Mora (Días)</label>
                                                                                                <input type="number" class="form-control form-control-sm shadow-none" name="parametros[mora_dias_max]" value="{{ $pOp['mora_dias_max'] ?? 0 }}" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                                                            </div>
                                                                                            <div class="col-6">
                                                                                                <label class="form-label text-muted mb-1" style="font-size: 0.65rem;">Días de Gracia</label>
                                                                                                <input type="number" class="form-control form-control-sm shadow-none" name="parametros[dias_gracia]" value="{{ $pOp['dias_gracia'] ?? 0 }}" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                                                            </div>
                                                                                        </div>

                                                                                        <label class="form-label text-muted mb-1" style="font-size: 0.65rem;">Observación / Justificación</label>
                                                                                        <textarea name="justificacion" class="form-control form-control-sm shadow-none" rows="2" style="font-size: 0.72rem; border-color: #cbd5e1;">{{ $conf->justificacion }}</textarea>
                                                                                    </div>

                                                                                    {{-- COLUMNA 2: EDICIÓN DE PARÁMETROS JSON (Activadores) --}}
                                                                                    <div class="col-md-4 border-end pe-md-3" style="border-color: #f1f5f9 !important;">
                                                                                        <span class="d-block fw-medium text-secondary mb-2" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.3px;"><i class="fas fa-bolt opacity-75 me-1 text-warning"></i> Activadores Lógicos</span>

                                                                                        <div class="d-flex flex-column gap-2.5 mt-2">
                                                                                            <div class="form-check form-switch m-0">
                                                                                                <input type="hidden" name="parametros[requiere_accion]" value="0">
                                                                                                <input class="form-check-input shadow-none" type="checkbox" role="switch" name="parametros[requiere_accion]" value="1" {{ !empty($pOp['requiere_accion']) ? 'checked' : '' }}>
                                                                                                <label class="form-check-label text-secondary" style="font-size: 0.72rem;">Requiere Acción Restrictiva</label>
                                                                                            </div>
                                                                                            <div class="form-check form-switch m-0">
                                                                                                <input type="hidden" name="parametros[bloqueo_automatico]" value="0">
                                                                                                <input class="form-check-input shadow-none" type="checkbox" role="switch" name="parametros[bloqueo_automatico]" value="1" {{ !empty($pOp['bloqueo_automatico']) ? 'checked' : '' }}>
                                                                                                <label class="form-check-label text-secondary" style="font-size: 0.72rem;">Bloqueo Automático Cupo</label>
                                                                                            </div>
                                                                                            <div class="form-check form-switch m-0">
                                                                                                <input type="hidden" name="parametros[notificacion_gerencia]" value="0">
                                                                                                <input class="form-check-input shadow-none" type="checkbox" role="switch" name="parametros[notificacion_gerencia]" value="1" {{ !empty($pOp['notificacion_gerencia']) ? 'checked' : '' }}>
                                                                                                <label class="form-check-label text-secondary" style="font-size: 0.72rem;">Notificación a Gerencia</label>
                                                                                            </div>
                                                                                            <div class="form-check form-switch m-0">
                                                                                                <input type="hidden" name="parametros[incluir_historico_3_anos]" value="0">
                                                                                                <input class="form-check-input shadow-none" type="checkbox" role="switch" name="parametros[incluir_historico_3_anos]" value="1" {{ !empty($pOp['incluir_historico_3_anos']) ? 'checked' : '' }}>
                                                                                                <label class="form-check-label text-secondary" style="font-size: 0.72rem;">Evaluación Histórica (3 Años)</label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- COLUMNA 3: ESTADO NATIVO Y GUARDADO --}}
                                                                                    <div class="col-md-4 ps-md-3 d-flex flex-column justify-content-between">
                                                                                        <div>
                                                                                            <span class="d-block fw-medium text-secondary mb-2" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.3px;"><i class="fas fa-shield-alt opacity-75 me-1 text-success"></i> Estado de la Regla</span>

                                                                                            <div class="mb-2">
                                                                                                <label class="form-label text-muted mb-1" style="font-size: 0.65rem;">Vigente Hasta</label>
                                                                                                <input type="date" class="form-control form-control-sm shadow-none" name="vigente_hasta" value="{{ $conf->vigente_hasta ? \Carbon\Carbon::parse($conf->vigente_hasta)->format('Y-m-d') : '' }}" style="font-size: 0.72rem; border-color: #cbd5e1;">
                                                                                            </div>

                                                                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                                                                <div class="form-check form-switch m-0">
                                                                                                    <input type="hidden" name="estado_notificacion" value="0">
                                                                                                    <input class="form-check-input shadow-none" type="checkbox" role="switch" name="estado_notificacion" value="1" {{ $conf->estado_notificacion ? 'checked' : '' }}>
                                                                                                    <label class="form-check-label text-secondary" style="font-size: 0.72rem;"><i class="fas fa-bell text-warning me-1"></i> Alertas Notificación</label>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="d-flex align-items-center gap-2">
                                                                                                <div class="form-check form-switch m-0">
                                                                                                    <input type="hidden" name="estado_activo" value="0">
                                                                                                    <input class="form-check-input shadow-none" type="checkbox" role="switch" name="estado_activo" value="1" {{ $conf->estado_activo ? 'checked' : '' }}>
                                                                                                    <label class="form-check-label fw-medium {{ $conf->estado_activo ? 'text-success' : 'text-danger' }}" style="font-size: 0.72rem;">{{ $conf->estado_activo ? 'Regla Activa' : 'Regla Inactiva' }}</label>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="mt-3">
                                                                                            <button type="submit" class="btn btn-sm btn-light border rounded-pill w-100 fw-medium shadow-none text-primary" style="font-size: 0.7rem; height: 28px; background-color: #f0f9ff; border-color: #bae6fd !important;">
                                                                                                <i class="fas fa-save me-1"></i> Guardar Cambios
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            </form>

                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                        @endforeach
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>

                                @else
                                    {{-- ESTADO VACÍO REDISEÑADO --}}
                                    <div class="text-center py-5 rounded mx-2" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-cogs fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Configuración Estándar</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">No hay parámetros excepcionales configurados para esta operación.</p>
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill mt-3 px-4 fw-medium text-muted shadow-none" style="font-size: 0.7rem;" onclick="$('#modalCrearConfiguracion').modal('show');">
                                            <i class="fas fa-plus me-1 text-primary"></i> Crear Excepción
                                        </button>
                                    </div>
                                @endif

                            </div>

                            {{-- ======================================================= --}}
                            {{-- TAB 1: LÍNEAS (Facturas/Registros de ERP)               --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade show active" id="lineas" role="tabpanel">

                                {{-- CABECERA / SUBTÍTULO + BOTÓN DE ACCIÓN GLOBAL --}}
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3 px-2">

                                    {{-- Mini-subtítulo Informativo (Suave y sin bordes bruscos) --}}
                                    <div class="d-flex align-items-center gap-2 py-2 px-3 rounded text-muted m-0 flex-grow-1" style="background-color: #f8fafc; font-size: 0.75rem; border: 1px solid #f1f5f9;">
                                        <i class="fas fa-info-circle opacity-50"></i>
                                        <span>
                                            Historial de <span class="fw-medium text-secondary">{{ $operacion->tercero ? Str::title(strtolower($operacion->tercero->nom_ter . ' ' . $operacion->tercero->apl1)) : 'Seleccionado' }}</span>.
                                            Contiene <span class="fw-medium text-secondary">{{ $lineasAgrupadas->count() }} {{ $lineasAgrupadas->count() == 1 ? 'línea' : 'líneas' }}</span> de facturación.
                                        </span>
                                    </div>

                                    {{-- Botón de Acción Global: Informe Cliente (Minimalista Estilo Píldora) --}}
                                    <div class="d-flex align-items-center flex-shrink-0">
                                        <a href="{{ route('certificados.operaciones.informe_cliente', $operacion->id) }}"
                                        target="_blank"
                                        class="btn btn-light border rounded-pill px-3 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center"
                                        style="font-size: 0.7rem; height: 32px; transition: all 0.2s ease; background-color: #f8fafc;"
                                        onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;"
                                        onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;"
                                        onclick="bloquearBotonUI(this, 'Generando...')">
                                            <i class="fas fa-chart-line me-1.5 text-primary opacity-75"></i> Informe Cliente
                                        </a>
                                    </div>

                                </div>

                                @if($lineasAgrupadas->count() > 0)

                                    {{-- CONTENEDOR PRINCIPAL --}}
                                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px; overflow: hidden; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">

                                        @foreach($lineasAgrupadas as $nombreLinea => $datosLinea)
                                            {{-- Agrupador de Línea (Botón + Tabla independiente) --}}
                                            <div class="border-bottom" style="border-color: #f1f5f9 !important;">

                                                {{-- HEADER DEL ACCORDION --}}
                                                <button class="btn d-block w-100 text-start p-3 shadow-none collapsed rounded-0 border-0"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseLinea-{{ $loop->index }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapseLinea-{{ $loop->index }}"
                                                        style="background-color: #ffffff; transition: background-color 0.15s ease;"
                                                        onmouseover="this.style.backgroundColor='#f8fafc';"
                                                        onmouseout="this.style.backgroundColor='#ffffff';">

                                                    <div class="row align-items-center w-100 m-0" style="font-size: 0.72rem;">

                                                        {{-- Columna 1: Título de la Línea y Badge --}}
                                                        <div class="col-12 col-md-6 d-flex align-items-center gap-2 p-0 mb-2 mb-md-0">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-white border flex-shrink-0" style="width: 24px; height: 24px; border-color: #e2e8f0 !important; color: #64748b;">
                                                                <i class="fas fa-layer-group opacity-75" style="font-size: 0.65rem;"></i>
                                                            </div>
                                                            <span class="fw-medium text-secondary text-truncate" style="letter-spacing: -0.1px;">
                                                                {{ Str::title(strtolower($nombreLinea)) }}
                                                            </span>
                                                            <span class="badge rounded-pill" style="padding: 0.2rem 0.5rem; font-size: 0.58rem; font-weight: 400; background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;">
                                                                Siasoft
                                                            </span>
                                                        </div>

                                                        {{-- Columna 2: Totales y Flecha --}}
                                                        <div class="col-12 col-md-6 p-0 d-flex align-items-center justify-content-md-end gap-3 text-muted" style="font-size: 0.7rem;">
                                                            <span class="d-flex align-items-center gap-1">
                                                                <i class="fas fa-file-invoice opacity-50"></i> {{ $datosLinea['count'] }} facturas
                                                            </span>
                                                            <span class="d-flex align-items-center gap-1 fw-medium text-secondary">
                                                                <i class="fas fa-dollar-sign opacity-50"></i> {{ number_format((float)$datosLinea['total'], 2) }}
                                                            </span>
                                                            <i class="fas fa-chevron-down opacity-50 ms-2" style="font-size: 0.6rem;"></i>
                                                        </div>

                                                    </div>
                                                </button>

                                                {{-- CUERPO COLAPSABLE CON TABLA INDEPENDIENTE --}}
                                                <div id="collapseLinea-{{ $loop->index }}" class="collapse" style="background-color: #ffffff;">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm align-middle mb-0 text-nowrap" style="font-size: 0.72rem; border-collapse: separate; border-spacing: 0;">

                                                            {{-- Cabeceras de las Columnas --}}
                                                            <thead class="text-uppercase text-secondary" style="font-size: 0.6rem; letter-spacing: 0.3px; background-color: #f8fafc;">
                                                                <tr>
                                                                    <th class="ps-4 pe-2 py-3 border-bottom fw-medium text-start" style="width: 12%; border-color: #e2e8f0 !important;">N° Factura</th>
                                                                    <th class="px-2 py-3 border-bottom fw-medium text-center" style="width: 6%; border-color: #e2e8f0 !important;">Cuota</th>
                                                                    <th class="px-2 py-3 border-bottom fw-medium text-start" style="width: 9%; border-color: #e2e8f0 !important;">Pagaré</th>
                                                                    <th class="px-2 py-3 border-bottom fw-medium text-center" style="width: 10%; border-color: #e2e8f0 !important;">Vence</th>
                                                                    <th class="px-2 py-3 border-bottom fw-medium text-center" style="width: 8%; border-color: #e2e8f0 !important;">Mora</th>
                                                                    <th class="px-3 py-3 border-bottom fw-medium text-end" style="width: 14%; border-color: #e2e8f0 !important;">V. Inicial</th>
                                                                    <th class="px-3 py-3 border-bottom fw-medium text-end" style="width: 14%; border-color: #e2e8f0 !important;">V. Neto</th>
                                                                    <th class="px-2 py-3 border-bottom fw-medium text-center" style="width: 13%; border-color: #e2e8f0 !important;">Soporte</th>
                                                                    <th class="pe-4 ps-2 py-3 border-bottom fw-medium text-center" style="width: 14%; border-color: #e2e8f0 !important;">Estado</th>
                                                                </tr>
                                                            </thead>

                                                            {{-- Filas de Datos (Facturas individuales) --}}
                                                            <tbody class="border-top-0">
                                                                @foreach($datosLinea['facturas'] as $factura)
                                                                    <tr class="border-bottom" style="border-color: #f1f5f9 !important; transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='transparent';">
                                                                        <td class="ps-4 pe-2 py-3 text-secondary font-monospace" style="font-size: 0.72rem;">{{ $factura->id_factura ?? 'N/A' }}</td>
                                                                        <td class="px-2 py-3 text-center text-muted">{{ $factura->cuota ?? '-' }}</td>
                                                                        <td class="px-2 py-3 text-muted text-start">{{ $factura->pagare ?? 'S/N' }}</td>

                                                                        <td class="px-2 py-3 text-center text-muted">
                                                                            @if($factura->fecha_venci)
                                                                                {{ $factura->fechaVFormateada }}
                                                                            @else
                                                                                <span class="opacity-25">-</span>
                                                                            @endif
                                                                        </td>

                                                                        <td class="px-2 py-3 text-center">
                                                                            @if($factura->diasMoraCalculados < 0)
                                                                                <span class="badge rounded-pill" style="background-color: #fee2e2; color: #b91c1c; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">{{ abs(intval($factura->diasMoraCalculados)) }} d</span>
                                                                            @else
                                                                                <span class="text-muted opacity-50">0</span>
                                                                            @endif
                                                                        </td>

                                                                        <td class="px-3 py-3 text-end text-muted">${{ number_format((float)$factura->valor_inicial, 2) }}</td>
                                                                        <td class="px-3 py-3 text-end fw-medium text-secondary">${{ number_format((float)$factura->valor, 2) }}</td>

                                                                        {{-- LÓGICA DEL COMPROBANTE --}}
                                                                        <td class="px-2 py-3 text-center">
                                                                            @if($factura->comprobantePago)
                                                                                <a href="{{ $factura->comprobantePago->url_archivo }}" target="_blank" class="badge rounded-pill text-decoration-none d-inline-flex align-items-center gap-1" style="background-color: #d1fae5; color: #047857; padding: 0.25rem 0.6rem; font-size: 0.6rem; font-weight: 500;" title="Ver Archivo: {{ $factura->comprobantePago->nombre_archivo_simple }}">
                                                                                    <i class="fas fa-file-invoice-dollar opacity-75"></i> Pago
                                                                                </a>
                                                                                <div class="mt-0.5 text-muted" style="font-size: 0.6rem;">
                                                                                    ${{ number_format((float)$factura->comprobantePago->monto_pagado, 0) }}
                                                                                </div>
                                                                            @else
                                                                                <a href="{{ route('interactions.create') }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center" style="font-size: 0.62rem; height: 22px; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;" onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;">
                                                                                    <i class="fas fa-plus me-1 text-primary"></i> Agregar
                                                                                </a>
                                                                            @endif
                                                                        </td>

                                                                        {{-- ESTADO --}}
                                                                        <td class="pe-4 ps-2 py-3 text-center">
                                                                            @if($factura->estado == 'PROCESADO')
                                                                                <span class="badge rounded-pill" style="background-color: #d1fae5; color: #047857; padding: 0.25rem 0.6rem; font-size: 0.6rem; font-weight: 400;">Procesado</span>
                                                                            @elseif($factura->anular == 1)
                                                                                <span class="badge rounded-pill" style="background-color: #fee2e2; color: #b91c1c; padding: 0.25rem 0.6rem; font-size: 0.6rem; font-weight: 400;">Anulado</span>
                                                                            @else
                                                                                <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; padding: 0.25rem 0.6rem; font-size: 0.6rem; font-weight: 400;">Pendiente</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Empty State Suave --}}
                                    <div class="text-center py-5 rounded mx-2" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-database fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Sin Datos en Siasoft</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">No se encontraron facturas asociadas a este cliente.</p>
                                    </div>
                                @endif

                            </div>

                            {{-- ======================================================= --}}
                            {{-- TAB 2: ALERTAS                                          --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="alertas" role="tabpanel">

                                {{-- HEADER DE LA SECCIÓN --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Registro de Alertas</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Control y programación de avisos o notificaciones para esta operación.</p>
                                    </div>

                                    {{-- Botón "Nueva Alerta" Estilo Píldora Minimalista --}}
                                    <button type="button"
                                            class="btn btn-light border rounded-pill px-3 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center"
                                            style="font-size: 0.7rem; height: 26px; transition: all 0.2s ease;"
                                            onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;"
                                            onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalAlerta">
                                        <i class="fas fa-bell me-1.5 text-primary opacity-75"></i> Nueva Alerta
                                    </button>
                                </div>

                                @if($historialAlertas->count() > 0)
                                    <div class="row g-2 px-2">
                                        @foreach($historialAlertas as $alerta)
                                            @php $esAlertaBloque = is_null($alerta->id_car_sia_operaciones); @endphp
                                            <div class="col-12">
                                                <div class="p-3 bg-white rounded-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center shadow-sm border" style="border-color: #f1f5f9 !important; transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='#ffffff';">

                                                    {{-- Info principal de la Alerta --}}
                                                    <div class="d-flex align-items-center gap-3 mb-2 mb-sm-0">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;">
                                                            <i class="fas fa-bell" style="font-size: 0.8rem;"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium text-secondary d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                                                                <span>{{ $alerta->tipoAlerta->nombre ?? 'Tipo de Alerta Desconocido' }}</span>
                                                                @if($esAlertaBloque)
                                                                    <span class="badge rounded-pill" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                        <i class="fas fa-layer-group opacity-75 me-1"></i> Lote
                                                                    </span>
                                                                @else
                                                                    <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                        <i class="fas fa-user opacity-75 me-1"></i> Cliente
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="text-muted mt-1 d-flex align-items-center flex-wrap gap-3" style="font-size: 0.7rem;">
                                                                <span><i class="far fa-calendar-alt opacity-50 me-1"></i> Programada: <span class="fw-medium text-secondary">{{ $alerta->fecha_programada ? \Carbon\Carbon::parse($alerta->fecha_programada)->format('d/m/Y') : 'N/A' }}</span></span>
                                                                <span><i class="fas fa-user-edit opacity-50 me-1"></i> Creada por: <span class="fw-medium text-secondary">{{ optional($alerta->usuario)->name ?? 'Sistema' }}</span></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Estado (Procesada / Pendiente) --}}
                                                    <div class="text-sm-end ms-5 ms-sm-0">
                                                        @if($alerta->procesado_en)
                                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #d1fae5; color: #047857; padding: 0.3rem 0.7rem; font-size: 0.65rem; font-weight: 500;">
                                                                <i class="fas fa-check opacity-75"></i> Procesada
                                                            </span>
                                                            <div class="text-muted mt-1" style="font-size: 0.62rem;">El {{ \Carbon\Carbon::parse($alerta->procesado_en)->format('d/m/Y h:i A') }}</div>
                                                        @else
                                                            <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; padding: 0.3rem 0.7rem; font-size: 0.65rem; font-weight: 500;">
                                                                <i class="fas fa-hourglass-half opacity-75"></i> Pendiente
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Empty State Suave --}}
                                    <div class="text-center py-5 rounded mx-2" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-bell-slash fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Sin Alertas Programadas</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">No hay recordatorios ni notificaciones asociadas a esta operación.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- ======================================================= --}}
                            {{-- TAB 3: HISTORIAL ETL / ESTADO                           --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="historial" role="tabpanel">
                                <div class="row g-4 px-2">

                                    {{-- COLUMNA 1: Transiciones de Estado --}}
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Transiciones de Estado</h5>
                                                <p class="text-muted mb-0" style="font-size: 0.75rem;">Historial de cambios de estado en la operación.</p>
                                            </div>

                                            {{-- Botón Estilo Píldora Minimalista --}}
                                            <button type="button"
                                                    class="btn btn-light border rounded-pill px-3 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center"
                                                    style="font-size: 0.7rem; height: 26px; transition: all 0.2s ease;"
                                                    onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;"
                                                    onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalTransicionar">
                                                <i class="fas fa-exchange-alt me-1.5 text-primary opacity-75"></i> Cambiar Estado
                                            </button>
                                        </div>

                                        @if($historialEstados->count() > 0)
                                            <div class="border-start border-2 ms-3 ps-4 position-relative py-2" style="border-color: #cbd5e1 !important;">
                                                @foreach($historialEstados as $historialEstado)
                                                    @php $esEstadoBloque = is_null($historialEstado->id_car_sia_operaciones); @endphp
                                                    <div class="mb-4 position-relative">
                                                        {{-- Indicador de la línea de tiempo --}}
                                                        <span class="position-absolute bg-white border border-2 rounded-circle" style="width: 12px; height: 12px; left: -1.82rem; top: 0.2rem; border-color: #0284c7 !important;"></span>

                                                        <div class="fw-medium text-secondary d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.8rem;">
                                                            <span>{{ $historialEstado->estado->nombre ?? 'Estado Desconocido' }}</span>
                                                            @if($esEstadoBloque)
                                                                <span class="badge rounded-pill" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                    <i class="fas fa-layer-group opacity-75 me-1"></i> API-{{ str_pad($historialEstado->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                </span>
                                                            @else
                                                                <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                    <i class="fas fa-user opacity-75 me-1"></i> Individual
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="text-muted mt-1 d-flex flex-wrap gap-2" style="font-size: 0.7rem;">
                                                            <span><i class="far fa-clock opacity-50 me-1"></i> {{ $historialEstado->created_at ? $historialEstado->created_at->format('d M, Y h:i A') : 'Fecha no disponible' }}</span>
                                                            <span class="opacity-50">|</span>
                                                            <span><i class="fas fa-user-tag opacity-50 me-1"></i> {{ optional($historialEstado->usuario)->name ?? 'Sistema' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Sin historial de estados registrado.</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- COLUMNA 2: Eventos Inyectados --}}
                                    <div class="col-md-6">
                                        <div class="mb-4" style="padding-top: 2px;">
                                            <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Eventos Inyectados</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.75rem;">Registro de eventos y tipos aplicados en el sistema.</p>
                                        </div>

                                        @if($historialTipos->count() > 0)
                                            <div class="border-start border-2 ms-3 ps-4 position-relative py-2" style="border-color: #cbd5e1 !important;">
                                                @foreach($historialTipos as $historialTipo)
                                                    <div class="mb-4 position-relative">
                                                        {{-- Indicador de la línea de tiempo --}}
                                                        <span class="position-absolute bg-white border border-2 rounded-circle" style="width: 12px; height: 12px; left: -1.82rem; top: 0.2rem; border-color: #0d9488 !important;"></span>

                                                        <div class="fw-medium text-secondary d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.8rem;">
                                                            <span>{{ $historialTipo->tipo->nombre ?? 'Tipo Desconocido' }}</span>
                                                            @if($historialTipo->es_lote)
                                                                <span class="badge rounded-pill" style="background-color: #f0fdf4; color: #0d9488; border: 1px solid #ccfbf1; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                    <i class="fas fa-layer-group opacity-75 me-1"></i> API-{{ str_pad($historialTipo->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                </span>
                                                            @else
                                                                <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                                    <i class="fas fa-user opacity-75 me-1"></i> Individual
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="text-muted mt-1 d-flex flex-wrap gap-2" style="font-size: 0.7rem;">
                                                            <span><i class="far fa-clock opacity-50 me-1"></i> {{ $historialTipo->created_at ? $historialTipo->created_at->format('d M, Y h:i A') : 'Fecha no disponible' }}</span>
                                                            <span class="opacity-50">|</span>
                                                            <span><i class="fas fa-user-edit opacity-50 me-1"></i> {{ $historialTipo->nombre_user ?? 'Sistema' }}{{ $historialTipo->cargo_user ?? '' }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                                <p class="mb-0 text-muted" style="font-size: 0.75rem;">Sin eventos inyectados.</p>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            {{-- ======================================================= --}}
                            {{-- TAB 4: CERTIFICADOS (Generador PDF y Tabla tipo Excel)  --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="certificados" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="fw-bold text-muted m-0 fs-8 text-uppercase"><i class="fas fa-file-pdf me-2"></i> Gestión de Certificados</h6>
                                    <button type="button" class="btn bg-pastel-danger shadow-sm rounded-pill px-3 fw-bold d-flex align-items-center hover-opacity" data-bs-toggle="modal" data-bs-target="#modalTipo">
                                        <i class="fas fa-file-pdf me-2 opacity-75"></i> Generar Certificado
                                    </button>
                                </div>
                                @if($operacion->lineas && $operacion->lineas->count() > 0)

                                    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                                        <div class="table-responsive">
                                            {{-- ID AGREGADO AQUÍ PARA CONTROLAR EL ACORDEÓN --}}
                                            <table class="table table-sm table-bordered align-middle mb-0" id="acordeonCertificados" style="font-size: 0.75rem;">
                                                @foreach($historialTipos as $registro)
                                                    @php
                                                        $certId = $loop->iteration;
                                                        $tipo = $registro->tipo;
                                                        $versionesDeEsteTipo = $registro->versionesDeEsteTipo;
                                                        $hashActual = $registro->hashActual;
                                                        $lineasEditor = $registro->lineasEditor;
                                                    @endphp

                                                   {{-- Fila Agrupadora (Ultra Minimalista, Suave al Ojo y Simétrica) --}}
                                                    <thead class="bg-white">
                                                        <tr>
                                                            <th class="p-0 border-0">
                                                                {{-- Botón agolpador: Fuerzo alineación izquierda, display block y ancho 100% para evitar centrado automático --}}
                                                                <button class="btn d-block w-100 text-start p-3 border-bottom shadow-none collapsed"
                                                                        type="button"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#collapseCertificado-{{ $certId }}"
                                                                        aria-expanded="false"
                                                                        aria-controls="collapseCertificado-{{ $certId }}"
                                                                        style="background-color: #fcfdfd; transition: background-color 0.2s ease; border-color: #f1f5f9 !important;">

                                                                    {{-- Contenedor simétrico: w-100 y m-0 son vitales para mantener la grilla estable --}}
                                                                    <div class="row align-items-center w-100 m-0" style="font-size: 0.75rem;">

                                                                        {{-- Columna 1: Icono y Título (Title Case para que sea más natural) --}}
                                                                        <div class="col-12 col-md-5 d-flex align-items-center gap-3 p-0 mb-2 mb-md-0">
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-white border shadow-sm flex-shrink-0" style="width: 28px; height: 28px; border-color: #e2e8f0 !important;">
                                                                                <i class="fas fa-file-pdf text-danger" style="font-size: 0.8rem;"></i>
                                                                            </div>
                                                                            <span class="fw-semibold text-secondary text-truncate" style="letter-spacing: 0.2px;">
                                                                                {{ Str::title(strtolower($tipo->nombre ?? 'Documento ' . $certId)) }}
                                                                            </span>
                                                                        </div>

                                                                        {{-- Columna 2: Lote / Tipo (Badges súper sutiles) --}}
                                                                        <div class="col-4 col-md-2 p-0">
                                                                            @if($registro->es_lote)
                                                                                <span class="badge rounded-pill fw-medium border" style="padding: 0.35rem 0.6rem; font-size: 0.65rem; background-color: #f1f5f9; color: #475569; border-color: #e2e8f0 !important;">
                                                                                    <i class="fas fa-layer-group me-1 opacity-75"></i> Lote: {{ str_pad($registro->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                                                </span>
                                                                            @else
                                                                                <span class="badge rounded-pill fw-medium border" style="padding: 0.35rem 0.6rem; font-size: 0.65rem; background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important;">
                                                                                    <i class="fas fa-user me-1 opacity-75"></i> Individual
                                                                                </span>
                                                                            @endif
                                                                        </div>

                                                                        {{-- Columna 3: Fecha y Hora (Discreta y alineada) --}}
                                                                        <div class="col-5 col-md-3 p-0 text-muted d-flex align-items-center gap-2" style="font-size: 0.7rem;">
                                                                            <i class="far fa-clock opacity-50"></i>
                                                                            <span>{{ $registro->created_at ? $registro->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                                                        </div>

                                                                        {{-- Columna 4: Usuario discreto + Flecha (Totalmente a la derecha) --}}
                                                                        <div class="col-3 col-md-2 p-0 d-flex align-items-center justify-content-end gap-2 text-muted">
                                                                            <span class="text-truncate fw-medium" style="font-size: 0.7rem;" title="{{ $registro->nombre_user ?? 'Sistema' }}">
                                                                                <i class="fas fa-user-circle me-1 opacity-50"></i>{{ Str::before($registro->nombre_user ?? 'Sistema', ' ') }}
                                                                            </span>
                                                                            <i class="fas fa-chevron-down opacity-50 ms-1" style="font-size: 0.65rem;"></i>
                                                                        </div>

                                                                    </div>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    {{-- Cuerpo del Certificado (Controles, PDF y Editor en linea) --}}
                                                    {{-- ATRIBUTO data-bs-parent AGREGADO AQUÍ PARA CERRAR LOS DEMÁS --}}
                                                    <tbody id="collapseCertificado-{{ $certId }}" class="collapse border-bottom" data-bs-parent="#acordeonCertificados" style="border-bottom-width: 1px !important; border-color: #f1f5f9 !important;">
                                                        <tr>
                                                            <td class="p-2 p-md-3 bg-white border-0" style="max-width: 0; width: 100%;">

                                                                @php
                                                                    $telefonoDestino = $operacion->tercero->tel ?? '0000000000';
                                                                    $nombreCliente   = $operacion->tercero->nom_ter ?? 'Nombre del Cliente';
                                                                    $numeroCredito   = $operacion->numero_radicado ?? $operacion->id;
                                                                    $remitente1      = Auth::user()->name ?? 'Asesor';
                                                                    $remitente2      = 'SIA Cartera';

                                                                    $urlPdf = route('certificados.operaciones.pdf_individual', [
                                                                        'id'      => $operacion->id,
                                                                        'tipo_id' => $tipo->id ?? null,
                                                                        'hash'    => $hashActual
                                                                    ]);

                                                                    $mensajeTexto = "Dios lo bendiga Hermano: \n*{$nombreCliente}*\nAdjunto encontrará el reporte de su estado de cuenta del crédito {$numeroCredito}\nPor favor, verificar si tiene alguna novedad o inquietud frente a la información suministrada.\n\nQuedo atento a cualquier comentario o sugerencia que desee compartir.\n\n*Gracias por su atención.*\nCordialmente;\n_{$remitente1}_\n_{$remitente2}_\n\n*Nota:* Si tiene problemas con el enlace adjunto, contáctenos por este medio.\n\n*Adjunto Documento:* \n{$urlPdf}";

                                                                    $linkWhatsapp = "https://api.whatsapp.com/send?phone=57{$telefonoDestino}&text=" . urlencode($mensajeTexto);
                                                                @endphp

                                                                {{-- HEADER: Jerarquía Limpia y Suave al Ojo (Sin Negritas Pesadas) --}}
                                                                <div class="d-flex flex-wrap align-items-center justify-content-between bg-white border rounded-3 p-2.5 mb-2.5 shadow-sm gap-2.5 w-100" style="border-color: #eaeeed !important;">

                                                                    {{-- Lado Izquierdo: Contexto amigable --}}
                                                                    <div class="d-flex align-items-center gap-2.5">
                                                                        <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 34px; height: 34px; background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                                                                            <i class="fas fa-file-invoice" style="font-size: 0.85rem;"></i>
                                                                        </div>
                                                                        <div>
                                                                            <div class="text-dark fw-medium" style="font-size: 0.8rem; letter-spacing: -0.1px;">Documento en Gestión</div>
                                                                            <div class="text-muted" style="font-size: 0.68rem;">Visualiza, edita los datos o notifica por WhatsApp</div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Lado Derecho: Controles compactos y suaves --}}
                                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                                        <div class="d-flex bg-light p-0.5 rounded-pill border" style="border-color: #e2e8f0 !important;">
                                                                            <button type="button" class="btn btn-sm btn-dark rounded-pill fw-medium border-0 px-2.5 py-1" style="font-size: 0.7rem;" id="btnModePdf_{{ $certId }}" onclick="toggleMode('pdf', '{{ $certId }}')">
                                                                                Ver PDF
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm text-secondary rounded-pill fw-medium border-0 px-2.5 py-1 bg-transparent" style="font-size: 0.7rem;" id="btnModeData_{{ $certId }}" onclick="toggleMode('data', '{{ $certId }}')">
                                                                                Datos
                                                                            </button>
                                                                        </div>

                                                                        @if($versionesDeEsteTipo->count() > 1)
                                                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fw-medium bg-white shadow-sm" style="font-size: 0.7rem; color: #475569; border-color: #cbd5e1;" data-bs-toggle="modal" data-bs-target="#modalVersiones_{{ $certId }}">
                                                                                <i class="fas fa-history me-1 opacity-75"></i> Versiones ({{ $versionesDeEsteTipo->count() }})
                                                                            </button>
                                                                        @endif

                                                                        <a href="{{ $linkWhatsapp }}" target="_blank" class="btn btn-sm rounded-pill px-2.5 py-1 fw-medium d-flex align-items-center shadow-sm" style="font-size: 0.7rem; background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                                                            <i class="fab fa-whatsapp me-1" style="font-size: 0.75rem;"></i> Enviar
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                {{-- CONTENEDOR 1: VISOR PDF --}}
                                                                <div id="pdfViewerContainer_{{ $certId }}" class="border rounded-3 overflow-hidden shadow-sm mb-2 w-100" style="height: 60vh; min-height: 350px; background-color: #f8fafc; border-color: #eaeeed !important;">
                                                                    <iframe src="{{ route('certificados.operaciones.pdf_individual', ['id' => $operacion->id, 'tipo_id' => $tipo->id ?? null, 'hash' => $hashActual]) }}" width="100%" height="100%" frameborder="0"></iframe>
                                                                </div>

                                                                {{-- CONTENEDOR 2: EDITOR DE DATOS --}}
                                                                <div id="dataEditorContainer_{{ $certId }}" class="d-none w-100">
                                                                    <form id="formEditor_{{ $certId }}" action="{{ route('certificados.operaciones.actualizar_lineas', $operacion->id) }}" method="POST">
                                                                        @csrf @method('PUT')
                                                                        <input type="hidden" name="tipo_certificado_id" value="{{ $tipo->id ?? '' }}">
                                                                        <input type="hidden" name="dias_gracia_lote" id="input_dias_gracia_{{ $certId }}" value="">

                                                                        {{-- Info Bar Suave (Sin negritas pesadas) --}}
                                                                        <div class="d-flex justify-content-between align-items-center rounded-pill py-1.5 px-3 mb-2.5 shadow-sm w-100" style="background-color: #f8fafc; border: 1px solid #eaeeed;">
                                                                            <div class="d-flex align-items-center text-secondary" style="font-size: 0.72rem;">
                                                                                <i class="fas fa-pen-nib me-2 text-muted"></i> Zona de edición habilitada (Modifica los campos necesarios)
                                                                            </div>
                                                                            <button type="button" class="btn btn-sm bg-white border fw-medium rounded-pill px-2.5 py-0.5 shadow-sm text-secondary" style="font-size: 0.68rem; border-color: #e2e8f0 !important;" data-bs-toggle="modal" data-bs-target="#modalParametros_{{ $certId }}">
                                                                                <i class="fas fa-sliders-h me-1 text-muted"></i> Días de Gracia
                                                                            </button>
                                                                        </div>

                                                                        {{-- Tabla Ultra Minimalista con Tonalidad Clara en Zona Editable --}}
                                                                        <div class="table-responsive border shadow-sm rounded-3 mb-3 bg-white w-100" style="border-color: #eaeeed !important;">
                                                                            <table class="table table-borderless align-middle mb-0" style="font-size: 0.72rem; width: 100%;">

                                                                                <thead>
                                                                                    <tr style="border-bottom: 2px solid #eaeeed; background-color: #f8fafc;">
                                                                                        {{-- Solo Lectura (Tipografía suave, sin uppercase recargado) --}}
                                                                                        <th class="py-2 px-3 fw-medium text-secondary text-nowrap" style="font-size: 0.68rem;">Factura</th>
                                                                                        <th class="py-2 px-2 fw-medium text-center text-secondary text-nowrap" style="font-size: 0.68rem;">Cuota</th>
                                                                                        <th class="py-2 px-3 fw-medium text-end text-secondary text-nowrap" style="font-size: 0.68rem;">Valor</th>
                                                                                        <th class="py-2 px-3 fw-medium text-center text-secondary text-nowrap" style="font-size: 0.68rem;">Comprobante</th>
                                                                                        <th class="py-2 px-3 fw-medium text-center text-secondary text-nowrap" style="font-size: 0.68rem;">Estado</th>
                                                                                        <th class="py-2 px-3 fw-medium border-end text-secondary text-nowrap" style="font-size: 0.68rem;">Cuenta</th>

                                                                                        {{-- Zona Editable (Tonalidad pasteles sutiles #eef2f6) --}}
                                                                                        <th class="py-2 px-3 fw-medium text-center text-dark text-nowrap border-start" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 130px;">Calif.</th>
                                                                                        <th class="py-2 px-3 fw-medium text-center text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 120px;">Est. SIA</th>
                                                                                        <th class="py-2 px-3 fw-medium text-center text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 120px;">Est. API</th>
                                                                                        <th class="py-2 px-2 fw-medium text-center text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 80px;">Mora</th>
                                                                                        <th class="py-2 px-2 fw-medium text-center text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 120px;">Vence</th>
                                                                                        <th class="py-2 px-2 fw-medium text-center text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 120px;">Últ. Rec.</th>
                                                                                        <th class="py-2 px-2 fw-medium text-center text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 120px;">Procesado</th>
                                                                                        <th class="py-2 px-3 fw-medium text-start text-dark text-nowrap" style="background-color: #eef2f6; font-size: 0.68rem; min-width: 150px;">Nota</th>
                                                                                    </tr>
                                                                                </thead>

                                                                                <tbody>
                                                                                    @forelse($lineasEditor as $linea)
                                                                                        <tr class="border-bottom" style="border-color: #f1f5f9 !important;">
                                                                                            {{-- LECTURA --}}
                                                                                            <td class="px-3 py-2 bg-white text-nowrap">
                                                                                                <div class="fw-medium text-dark" style="font-family: monospace; font-size: 0.75rem;">#{{ $linea->id_factura }}</div>
                                                                                                <div class="text-muted" style="font-size: 0.62rem;">Lote: {{ str_pad($linea->numero_bloque, 4, '0', STR_PAD_LEFT) }}</div>
                                                                                            </td>
                                                                                            <td class="px-2 py-2 text-center text-secondary bg-white text-nowrap" style="font-size: 0.72rem;">{{ $linea->factura?->cuota ?? '-' }}</td>
                                                                                            <td class="px-3 py-2 text-end text-dark bg-white text-nowrap" style="font-size: 0.72rem;">${{ isset($linea->factura?->valor) ? number_format((float)$linea->factura->valor, 0, ',', '.') : '0' }}</td>

                                                                                            <td class="px-3 py-2 text-center bg-white text-nowrap">
                                                                                                @if($linea->factura?->comprobantePago)
                                                                                                    <a href="{{ $linea->factura->comprobantePago->url_archivo }}" target="_blank" class="badge text-decoration-none rounded-pill px-2 py-0.5 fw-normal" style="background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.62rem;">Pagado</a>
                                                                                                @else
                                                                                                    <a href="{{ route('interactions.create') }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2 py-0 text-muted shadow-none text-decoration-none" style="font-size: 0.62rem;">
                                                                                                        <i class="fas fa-plus me-1 text-primary"></i> Agregar
                                                                                                    </a>
                                                                                                @endif
                                                                                            </td>

                                                                                            <td class="px-3 py-2 text-center bg-white text-nowrap">
                                                                                                @if($linea->factura?->estado == 'PROCESADO')
                                                                                                    <span class="text-success" style="font-size: 0.62rem;">PROCESADO</span>
                                                                                                @elseif($linea->factura?->anular == 1)
                                                                                                    <span class="text-danger" style="font-size: 0.62rem;">ANULADO</span>
                                                                                                @else
                                                                                                    <span class="text-muted" style="font-size: 0.62rem;">PENDIENTE</span>
                                                                                                @endif
                                                                                            </td>

                                                                                            <td class="px-3 py-2 border-end bg-white">
                                                                                                <div class="text-dark text-nowrap" style="font-size: 0.68rem;">{{ $linea->id_car_sia_lineas }}</div>
                                                                                                <div class="text-muted text-truncate" style="font-size: 0.62rem; max-width: 130px;" title="{{ $linea->factura?->lineaSia?->nombre ?? 'N/A' }}">
                                                                                                    {{ $linea->factura?->lineaSia?->nombre ?? 'N/A' }}
                                                                                                </div>
                                                                                            </td>

                                                                                            {{-- EDITABLE (#eef2f6) --}}
                                                                                            <td class="p-0 align-middle text-nowrap border-start" style="background-color: #eef2f6; min-width: 130px;">
                                                                                                <div class="d-flex align-items-center justify-content-between px-2 py-1">
                                                                                                    <select name="lineas[{{ $linea->id }}][calificacion]" class="form-select form-select-sm border-0 shadow-none bg-transparent px-1 text-center {{ $linea->calificacion == 'Bueno' ? 'text-success' : ($linea->calificacion == 'Regular' ? 'text-warning' : 'text-danger') }}" style="font-size: 0.72rem;" onchange="this.className = 'form-select form-select-sm border-0 shadow-none bg-transparent px-1 text-center ' + (this.value == 'Bueno' ? 'text-success' : (this.value == 'Regular' ? 'text-warning' : 'text-danger'))">
                                                                                                        <option class="text-dark" value="Bueno" {{ $linea->calificacion == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                                                                                        <option class="text-dark" value="Regular" {{ $linea->calificacion == 'Regular' ? 'selected' : '' }}>Regular</option>
                                                                                                        <option class="text-dark" value="Irregular" {{ $linea->calificacion == 'Irregular' ? 'selected' : '' }}>Irregular</option>
                                                                                                    </select>
                                                                                                    <button type="button" class="btn btn-sm text-secondary p-1 ms-1 border-0 bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" data-bs-toggle="modal" data-bs-target="#modalExplicacion-{{ $linea->id }}" title="Ver Radiografía del Cálculo" style="width: 24px; height: 24px;">
                                                                                                        <i class="fas fa-microscope text-primary" style="font-size: 0.65rem;"></i>
                                                                                                    </button>
                                                                                                </div>
                                                                                            </td>

                                                                                            {{-- ESTADO SIA --}}
                                                                                            <td class="p-0 align-middle text-nowrap" style="background-color: #eef2f6; min-width: 120px;">
                                                                                                <select name="lineas[{{ $linea->id }}][id_car_sia_estados]" class="form-select form-select-sm border-0 shadow-none text-dark bg-transparent px-2 py-1 text-center" style="font-size: 0.72rem;">
                                                                                                    <option value="">Seleccione...</option>
                                                                                                    @foreach($estados as $est)
                                                                                                        <option value="{{ $est->id }}" {{ $linea->id_car_sia_estados == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </td>

                                                                                            {{-- ESTADO API --}}
                                                                                            <td class="p-0 align-middle text-nowrap" style="background-color: #eef2f6; min-width: 120px;">
                                                                                                <select name="lineas[{{ $linea->id }}][estadoApi]" class="form-select form-select-sm border-0 shadow-none bg-transparent px-2 py-1 text-center {{ $linea->estadoApi == 1 ? 'text-success' : 'text-muted' }}" style="font-size: 0.72rem;" onchange="actualizarEstadoApiDirecto(this, '{{ $linea->id_factura }}', '{{ $linea->numero_bloque }}')">
                                                                                                    <option value="" class="text-center" {{ is_null($linea->estadoApi) || $linea->estadoApi === '' ? 'selected' : '' }}>No Pago</option>
                                                                                                    <option value="1" class="text-center" {{ $linea->estadoApi == 1 ? 'selected' : '' }}>Pago</option>
                                                                                                </select>
                                                                                            </td>

                                                                                            <td class="p-0 align-middle text-nowrap px-1" style="background-color: #eef2f6;">
                                                                                                <input type="number" name="lineas[{{ $linea->id }}][dias_mora_automaticos]" class="form-control form-control-sm border-0 shadow-none text-center bg-transparent px-2 py-1 {{ $linea->dias_mora_automaticos < 0 ? 'text-danger' : 'text-dark' }}" value="{{ $linea->dias_mora_automaticos }}" style="font-size: 0.72rem; width: 70px;">
                                                                                            </td>

                                                                                            <td class="p-0 align-middle text-nowrap px-1" style="background-color: #eef2f6;">
                                                                                                <input type="date" name="lineas[{{ $linea->id }}][fecha_venci]" class="form-control form-control-sm border-0 shadow-none text-center bg-transparent text-secondary px-1 py-1" value="{{ $linea->fecha_venci ? \Carbon\Carbon::parse($linea->fecha_venci)->format('Y-m-d') : '' }}" style="font-size: 0.72rem;">
                                                                                            </td>

                                                                                            <td class="p-0 align-middle text-nowrap px-1" style="background-color: #eef2f6;">
                                                                                                <input type="date" name="lineas[{{ $linea->id }}][fecha_ultimo_recordatorio]" class="form-control form-control-sm border-0 shadow-none text-center bg-transparent text-secondary px-1 py-1" value="{{ $linea->fecha_ultimo_recordatorio ? \Carbon\Carbon::parse($linea->fecha_ultimo_recordatorio)->format('Y-m-d') : '' }}" style="font-size: 0.72rem;">
                                                                                            </td>

                                                                                            <td class="p-0 align-middle text-nowrap px-1" style="background-color: #eef2f6;">
                                                                                                <input type="date" name="lineas[{{ $linea->id }}][procesado_en]" class="form-control form-control-sm border-0 shadow-none text-center bg-transparent text-secondary px-1 py-1" value="{{ $linea->procesado_en ? \Carbon\Carbon::parse($linea->procesado_en)->format('Y-m-d') : '' }}" style="font-size: 0.72rem;">
                                                                                            </td>

                                                                                            {{-- NOTA / OBSERVACIÓN --}}
                                                                                            <td class="p-0 align-middle px-1" style="background-color: #eef2f6; min-width: 150px;">
                                                                                                <input type="text"
                                                                                                    name="lineas[{{ $linea->id }}][observacion]"
                                                                                                    class="form-control form-control-sm border-0 shadow-none bg-transparent text-dark px-2 py-1 text-truncate"
                                                                                                    value="{{ $linea->observacion }}"
                                                                                                    title="{{ $linea->observacion }}"
                                                                                                    placeholder="Escribe nota..."
                                                                                                    style="font-size: 0.72rem;">
                                                                                            </td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr>
                                                                                            <td colspan="14" class="text-center py-4 text-muted bg-white">
                                                                                                <p class="fs-7 mb-0">Sin líneas editables.</p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        {{-- Botón Guardar --}}
                                                                        @if($lineasEditor->count() > 0)
                                                                            <div class="d-flex justify-content-end mt-2">
                                                                                <button type="button" class="btn btn-dark rounded-pill px-4 py-1.5 fw-medium shadow-sm" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#modalConfirmSave_{{ $certId }}">
                                                                                    <i class="fas fa-save me-1 opacity-75"></i> Guardar Versión
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    </form>

                                                                    {{-- MODAL PARÁMETROS --}}
                                                                    <div class="modal fade" id="modalParametros_{{ $certId }}" tabindex="-1" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                                                            <div class="modal-content border-0 shadow rounded-4">
                                                                                <div class="modal-body p-4 text-center">
                                                                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 40px; height: 40px; background-color: #f1f5f9; color: #64748b;">
                                                                                        <i class="fas fa-sliders-h"></i>
                                                                                    </div>
                                                                                    <h6 class="fw-semibold text-dark mb-1" style="font-size: 0.9rem;">Días de Gracia</h6>
                                                                                    <p class="text-muted" style="font-size: 0.72rem;">Aplica días extra a este lote.</p>

                                                                                    <input type="number" class="form-control border text-center fw-medium text-dark rounded-3 mb-3 shadow-none" style="background-color: #f8fafc;" id="modal_input_gracia_{{ $certId }}" placeholder="0">

                                                                                    <div class="d-flex gap-2">
                                                                                        <button type="button" class="btn btn-light rounded-pill w-50 fw-medium border" style="font-size: 0.75rem;" data-bs-dismiss="modal">Cancelar</button>
                                                                                        <button type="button" class="btn btn-dark rounded-pill w-50 fw-medium" style="font-size: 0.75rem;" onclick="document.getElementById('input_dias_gracia_{{ $certId }}').value = document.getElementById('modal_input_gracia_{{ $certId }}').value;" data-bs-dismiss="modal">
                                                                                            Aplicar
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>

                                    {{-- ========================================== --}}
                                    {{-- BLOQUE DE MODALES PARA CADA TIPO DE CERTIFICADO --}}
                                    {{-- ========================================== --}}
                                    @foreach($historialTipos as $registro)
                                        @php
                                            $certId = $loop->iteration;
                                            $tipo = $registro->tipo;
                                            $versionesDeEsteTipo = $registro->versionesDeEsteTipo;
                                            $lineasEditor = $registro->lineasEditor;
                                        @endphp

                                        {{-- Modal de Historial de Versiones del Certificado --}}
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
                                                        <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                            <button type="button" class="btn btn-sm btn-light border rounded-1 px-4 shadow-sm" data-bs-target="#modalVersiones_{{ $certId }}" data-bs-toggle="modal" data-bs-dismiss="modal">
                                                                <i class="fas fa-arrow-left me-1"></i> Volver a Versiones
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Modal de Detalle de los datos guardados en el Hash específico --}}
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

                                        {{-- Modal Confirmación de Guardar Edición de Líneas --}}
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
                                                            <button type="button" class="btn btn-sm btn-success rounded-1 px-4 fw-bold shadow-sm" onclick="enviarFormularioRemoto('formEditor_{{ $certId }}', this)">
                                                                Confirmar y Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- BLOQUE DE MODALES DE RADIOGRAFÍA DEL CÁLCULO --}}
                                        @foreach($lineasEditor as $linea)
                                            @php
                                                $meta = is_string($linea->metadata) ? json_decode($linea->metadata, true) : (array) ($linea->metadata ?? []);
                                                $diasGracia = (int) ($meta['dias_gracia'] ?? 0);
                                                $diasTranscurridos = (int) ($linea->dias_mora_automaticos ?? 0);
                                                $moraEfectiva = max(0, $diasTranscurridos - $diasGracia);
                                                $clasificacion = $meta['clasificacion_mora'] ?? 'Indeterminada';
                                                $observacionFase = $meta['observacion_fase'] ?? '';
                                                $observacionGeneral = $linea->observacion ?? '';
                                            @endphp

                                            <div class="modal fade" id="modalExplicacion-{{ $linea->id }}" tabindex="-1" aria-labelledby="modalExplicacionLabel-{{ $linea->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                            <h5 class="fw-bold mb-0 text-primary" id="modalExplicacionLabel-{{ $linea->id }}">
                                                                <i class="fas fa-microscope me-2"></i> Radiografía del Cálculo
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                                <p class="text-muted fs-7 mb-0">Auditoría de calificación para la factura <strong class="text-dark font-monospace fs-6">#{{ $linea->id_factura }}</strong></p>
                                                                <span class="badge bg-light text-secondary border"><i class="fas fa-user-circle me-1"></i> {{ optional($linea->usuario)->name ?? 'Sistema' }}</span>
                                                            </div>

                                                            <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2" style="font-size: 0.85rem;"><i class="far fa-calendar-alt me-2"></i>1. Línea de Tiempo Base</h6>
                                                            <div class="row g-3 mb-4">
                                                                <div class="col-md-6">
                                                                    <div class="p-3 bg-light rounded-3 border border-light h-100">
                                                                        <span class="d-block text-muted" style="font-size: 0.70rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.25rem;">Fecha de Vencimiento</span>
                                                                        <span class="fs-6 fw-bold text-dark"><i class="far fa-calendar-times me-2 text-danger"></i>{{ $linea->fecha_venci ? \Carbon\Carbon::parse($linea->fecha_venci)->format('d/m/Y') : 'N/A' }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="p-3 bg-light rounded-3 border border-light h-100">
                                                                        <span class="d-block text-muted" style="font-size: 0.70rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.25rem;">Fecha de Corte (Día del Cálculo)</span>
                                                                        <span class="fs-6 fw-bold text-dark"><i class="far fa-calendar-check me-2 text-primary"></i>{{ $linea->created_at ? \Carbon\Carbon::parse($linea->created_at)->format('d/m/Y') : 'N/A' }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2" style="font-size: 0.85rem;"><i class="fas fa-calculator me-2"></i>2. Cálculo de Mora</h6>
                                                            <div class="row g-2 mb-2 align-items-center text-center">
                                                                <div class="col">
                                                                    <div class="p-3 bg-white rounded-3 border shadow-sm">
                                                                        <span class="d-block text-muted mb-1" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700;">Días Transcurridos</span>
                                                                        <span class="fs-4 fw-bold text-dark">{{ $diasTranscurridos }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <i class="fas fa-minus text-muted"></i>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-3 bg-white rounded-3 border shadow-sm">
                                                                        <span class="d-block text-muted mb-1" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700;">Días de Gracia</span>
                                                                        <span class="fs-4 fw-bold text-success">{{ $diasGracia }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <i class="fas fa-equals text-muted"></i>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="p-3 bg-white rounded-3 border shadow-sm border-danger border-opacity-50">
                                                                        <span class="d-block text-muted mb-1" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700;">Mora Efectiva</span>
                                                                        <span class="fs-4 fw-bold text-danger">{{ $moraEfectiva }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="text-muted text-center mb-4" style="font-size: 0.75rem;">La calificación se determinó basándose en <strong>{{ $moraEfectiva }} días</strong> de mora efectiva.</p>

                                                            <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2" style="font-size: 0.85rem;"><i class="fas fa-gavel me-2"></i>3. Dictamen del Sistema</h6>
                                                            <div class="alert bg-light border rounded-3 mb-0">
                                                                <div class="bg-white p-3 rounded border mb-3">
                                                                    <p class="text-dark font-monospace mb-0" style="font-size: 0.85rem;">
                                                                        > {{ $observacionGeneral ?: $observacionFase }}
                                                                    </p>
                                                                </div>
                                                                <div class="row align-items-center mb-3 text-center">
                                                                    <div class="col-6 border-end">
                                                                        <span class="d-block text-muted mb-1" style="font-size: 0.70rem; text-transform: uppercase;">Clasificación de Regla</span>
                                                                        <span class="badge bg-secondary text-uppercase">{{ $clasificacion }}</span>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <span class="d-block text-muted mb-1" style="font-size: 0.70rem; text-transform: uppercase;">Calificación Final</span>
                                                                        <span class="badge rounded-1 {{ ($linea->calificacion ?? '') == 'Bueno' ? 'bg-success' : (($linea->calificacion ?? '') == 'Regular' ? 'bg-warning text-dark' : 'bg-danger') }} text-uppercase fs-6">
                                                                            {{ $linea->calificacion ?? 'N/A' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 bg-light rounded-bottom-4">
                                                            <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach

                                @else
                                    <div class="text-center py-5 text-muted bg-light rounded-4 border-dashed">
                                        <i class="fas fa-file-excel fs-1 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-bold text-dark">Líneas No Estructuradas</h6>
                                        <p class="mb-0 fs-7">El certificado no cuenta con datos procesados aún. Utiliza el botón <strong class="text-danger">Generar Certificado</strong>.</p>
                                    </div>
                                @endif
                            </div>
                            {{-- Cierrte tab 4 --}}

                            {{-- ======================================================= --}}
                            {{-- TAB 5: GRÁFICOS E INDICADORES                           --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="graficos" role="tabpanel">

                                {{-- HEADER DE LA SECCIÓN --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Resumen Visual de la Operación</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Análisis estadístico de cartera y distribución de eventos del sistema.</p>
                                    </div>
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

                                <div class="row g-3 px-2">

                                    <!-- Gráfico 1: Estado de las Facturas -->
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 8px; border: 1px solid #f1f5f9 !important;">

                                            <div class="card-header bg-white border-bottom pt-3 pb-2 px-4 d-flex justify-content-between align-items-center" style="border-color: #f8fafc !important;">
                                                <span class="fw-medium text-secondary" style="font-size: 0.8rem;">
                                                    <i class="fas fa-chart-pie text-primary opacity-75 me-2"></i> Composición de Cartera
                                                </span>
                                                <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; font-size: 0.6rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                    Facturas
                                                </span>
                                            </div>

                                            <div class="card-body p-4 d-flex justify-content-center align-items-center" style="min-height: 260px;">
                                                <div style="width: 100%; max-width: 280px; height: 230px; position: relative;">
                                                    <canvas id="chartCartera"></canvas>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Gráfico 2: Tiempos o Tipos de Eventos -->
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 8px; border: 1px solid #f1f5f9 !important;">

                                            <div class="card-header bg-white border-bottom pt-3 pb-2 px-4 d-flex justify-content-between align-items-center" style="border-color: #f8fafc !important;">
                                                <span class="fw-medium text-secondary" style="font-size: 0.8rem;">
                                                    <i class="fas fa-chart-bar text-info opacity-75 me-2"></i> Distribución de Eventos (ETL)
                                                </span>
                                                <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #64748b; font-size: 0.6rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                    Auditoría
                                                </span>
                                            </div>

                                            <div class="card-body p-4 d-flex justify-content-center align-items-center" style="min-height: 260px;">
                                                <div style="width: 100%; height: 230px; position: relative;">
                                                    <canvas id="chartEventos"></canvas>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- ======================================================= --}}
                            {{-- TAB 6: OPERARIOS Y RENDIMIENTO                          --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="operarios" role="tabpanel">

                                {{-- HEADER DE LA SECCIÓN --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Índice de Intervención por Usuario</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Métricas de rendimiento y participación de operarios en la operación.</p>
                                    </div>
                                </div>

                                @if($operariosData->count() > 0)
                                    <div class="row g-3 px-2">
                                        @foreach($operariosData as $operario)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card border-0 shadow-sm h-100 bg-white p-3" style="border-radius: 8px; border: 1px solid #f1f5f9 !important; transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='#ffffff';">

                                                    {{-- Info del Operario --}}
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;">
                                                            <i class="fas fa-user text-primary opacity-75" style="font-size: 0.85rem;"></i>
                                                        </div>
                                                        <div class="overflow-hidden">
                                                            <h6 class="fw-medium text-secondary mb-0 text-truncate" style="font-size: 0.8rem; max-width: 170px;" title="{{ $operario['nombre'] }}">{{ $operario['nombre'] }}</h6>
                                                            <span class="text-muted d-block text-truncate" style="font-size: 0.68rem; max-width: 170px;" title="{{ $operario['cargo'] }}">{{ $operario['cargo'] }}</span>
                                                        </div>
                                                    </div>

                                                    {{-- Métricas / Procesos ejecutados --}}
                                                    <div class="mb-2 d-flex justify-content-between align-items-end">
                                                        <span class="text-muted" style="font-size: 0.7rem;">Procesos ejecutados</span>
                                                        <span class="fw-medium text-secondary" style="font-size: 0.9rem; letter-spacing: -0.2px;">{{ $operario['cantidad'] }}</span>
                                                    </div>

                                                    {{-- Barra de progreso minimalista --}}
                                                    <div class="progress mb-3" style="height: 4px; background-color: #f1f5f9; border-radius: 2px;">
                                                        <div class="progress-bar" role="progressbar" style="width: {{ $operario['porcentaje'] }}%; background-color: #0284c7; border-radius: 2px;" aria-valuenow="{{ $operario['porcentaje'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>

                                                    {{-- Footer de la Tarjeta --}}
                                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                                        <span class="text-muted" style="font-size: 0.65rem;"><i class="far fa-clock opacity-50 me-1"></i> {{ $operario['ultimo'] }}</span>
                                                        <span class="badge rounded-pill" style="background-color: #f0fdf4; color: #059669; border: 1px solid #a7f3d0; font-size: 0.58rem; font-weight: 400; padding: 0.2rem 0.5rem;">
                                                            {{ $operario['porcentaje'] }}% del total
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Empty State Suave --}}
                                    <div class="text-center py-5 rounded mx-2" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-user-slash fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Sin Intervenciones</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">Aún no hay registros de operarios en esta operación.</p>
                                    </div>
                                @endif

                            </div>

                            {{-- ======================================================= --}}
                            {{-- INICIO TAB 7: SOPORTES (FORMATO HOJA DE CÁLCULO PRO)    --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="soportes" role="tabpanel" aria-labelledby="soportes-tab" tabindex="0">

                                {{-- HEADER DE LA SECCIÓN --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Registro de Soportes Financieros</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Trazabilidad detallada de pagos, obligaciones y rutas.</p>
                                    </div>
                                    @if(isset($soportes) && $soportes->count() > 0)
                                        {{-- Badge total soportes estilo píldora minimalista --}}
                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                                            style="background-color: #d1fae5; color: #047857; border: 1px solid #a7f3d0; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 500;">
                                            <i class="fas fa-check-circle opacity-75"></i> {{ $soportes->count() }} Soportes validados
                                        </span>
                                    @endif
                                </div>

                                @if(isset($soportes) && $soportes->count() > 0)
                                    {{-- CONTENEDOR PRINCIPAL --}}
                                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px; overflow: hidden; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">
                                        <div class="table-responsive">

                                            <table class="table table-sm align-middle mb-0 text-nowrap" style="font-size: 0.72rem; border-collapse: separate; border-spacing: 0;">

                                                {{-- Cabeceras de las Columnas --}}
                                                <thead class="text-uppercase text-secondary" style="font-size: 0.6rem; letter-spacing: 0.3px; background-color: #f8fafc;">
                                                    <tr>
                                                        <th class="ps-4 pe-3 py-3 border-bottom fw-medium text-start" style="width: 18%; border-color: #e2e8f0 !important;">Transacción / Fecha</th>
                                                        <th class="px-3 py-3 border-bottom fw-medium text-start" style="width: 25%; border-color: #e2e8f0 !important;">Obligación / Banco</th>
                                                        <th class="px-3 py-3 border-bottom fw-medium text-start" style="width: 15%; border-color: #e2e8f0 !important;">Detalle & Cuota</th>
                                                        <th class="px-3 py-3 border-bottom fw-medium text-start" style="width: 15%; border-color: #e2e8f0 !important;">Responsable</th>
                                                        <th class="px-3 py-3 border-bottom fw-medium text-end" style="width: 14%; border-color: #e2e8f0 !important;">Monto Pagado</th>
                                                        <th class="pe-4 ps-3 py-3 border-bottom fw-medium text-center" style="width: 13%; border-color: #e2e8f0 !important;">Acción</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="border-top-0">
                                                    @foreach($soportes as $soporte)
                                                        <tr class="border-bottom" style="border-color: #f1f5f9 !important; transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='transparent';">

                                                            {{-- 1. Transacción y Fecha --}}
                                                            <td class="ps-4 pe-3 py-3">
                                                                <div class="d-flex flex-column gap-1">
                                                                    <span class="fw-medium text-secondary font-monospace" style="font-size: 0.75rem;">
                                                                        <i class="fas fa-hashtag text-muted opacity-50 me-1" style="font-size: 0.6rem;"></i>PR-{{ str_pad($soporte->pr, 5, '0', STR_PAD_LEFT) }}
                                                                    </span>
                                                                    <span class="text-muted" style="font-size: 0.68rem;">
                                                                        {{ $soporte->fecha_pago ? \Carbon\Carbon::parse($soporte->fecha_pago)->format('d M, Y - h:i A') : \Carbon\Carbon::parse($soporte->created_at)->format('d M, Y') }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            {{-- 2. Aplicación (Crédito/Banco) --}}
                                                            <td class="px-3 py-3">
                                                                <div class="d-flex flex-column gap-1">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge rounded-pill text-uppercase text-center" style="width: 50px; background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.55rem; font-weight: 400;">Crédito</span>
                                                                        <span class="fw-medium text-secondary text-truncate" style="max-width: 180px; font-size: 0.72rem;" title="{{ optional($soporte->obligacion)->nombre ?? 'Línea #' . $soporte->id_obligacion }}">
                                                                            {{ optional($soporte->obligacion)->nombre ?? 'Línea #' . $soporte->id_obligacion }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge rounded-pill text-uppercase text-center" style="width: 50px; background-color: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; font-size: 0.55rem; font-weight: 400;">Banco</span>
                                                                        <span class="text-muted text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="{{ optional($soporte->banco)->nombre_banco ?? 'Cuenta no especificada' }}">
                                                                            {{ optional($soporte->banco)->nombre_banco ?? 'Cuenta no especificada' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            {{-- 3. Detalle y Tipo de Pago --}}
                                                            <td class="px-3 py-3">
                                                                <div class="d-flex flex-column gap-1 align-items-start">
                                                                    <span class="text-secondary fw-medium" style="font-size: 0.72rem;">
                                                                        Cuota {{ $soporte->numero_cuota }}
                                                                    </span>
                                                                    <span class="badge rounded-pill border text-truncate" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; padding: 0.15rem 0.5rem; font-size: 0.6rem; font-weight: 400; max-width: 120px;" title="{{ $soporte->tipo_pago ?? 'Abono / Pago' }}">
                                                                        {{ $soporte->tipo_pago ?? 'Abono regular' }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            {{-- 4. Usuario Responsable --}}
                                                            <td class="px-3 py-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 24px; height: 24px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #64748b;">
                                                                        <i class="fas fa-user opacity-75" style="font-size: 0.55rem;"></i>
                                                                    </div>
                                                                    <span class="text-muted text-truncate" style="max-width: 110px; font-size: 0.72rem;" title="{{ optional($soporte->user)->name ?? 'Sistema / Externo' }}">
                                                                        {{ optional($soporte->user)->name ?? 'Sistema / Externo' }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            {{-- 5. Monto Pagado --}}
                                                            <td class="px-3 py-3 text-end">
                                                                <span class="fw-medium text-secondary" style="font-size: 0.8rem; letter-spacing: -0.2px;">
                                                                    ${{ number_format($soporte->monto_pagado, 0, ',', '.') }}
                                                                </span>
                                                            </td>

                                                            {{-- 6. Acción: Botón AWS S3 --}}
                                                            <td class="pe-4 ps-3 py-3 text-center">
                                                                @if($soporte->url_archivo && $soporte->url_archivo !== '#')
                                                                    <a href="{{ $soporte->url_archivo }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-2.5 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center" style="font-size: 0.62rem; height: 22px; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;" onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;">
                                                                        <i class="fas fa-cloud-download-alt me-1 text-primary opacity-75"></i> Ver
                                                                    </a>
                                                                @else
                                                                    <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #94a3b8; padding: 0.25rem 0.6rem; font-size: 0.6rem; font-weight: 400;">
                                                                        Sin archivo
                                                                    </span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    {{-- Empty State Suave --}}
                                    <div class="text-center py-5 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-folder-open fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Cero Soportes Registrados</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">Aún no se han procesado comprobantes físicos ni transferencias de esta operación.</p>
                                    </div>
                                @endif

                            </div>
                            {{-- ======================================================= --}}
                            {{-- FIN TAB 7 --}}
                            {{-- ======================================================= --}}

                            {{-- ======================================================= --}}
                            {{-- INICIO TAB 8: INTERACCIONES (CRM / CONTACTOS)           --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="interacciones" role="tabpanel" aria-labelledby="interacciones-tab" tabindex="0">

                                {{-- HEADER DE LA SECCIÓN (Minimalista y Suave) --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Registro de Interacciones</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Historial de llamadas, correos y gestiones realizadas con este cliente.</p>
                                    </div>
                                    @if(isset($interacciones) && $interacciones->count() > 0)
                                        {{-- Badge total interacciones estilo píldora minimalista --}}
                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                                            style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 500;">
                                            <i class="fas fa-history opacity-75"></i> {{ $interacciones->count() }} Gestiones
                                        </span>
                                    @endif
                                </div>

                                @if(isset($interacciones) && $interacciones->count() > 0)
                                    {{-- CONTENEDOR PRINCIPAL --}}
                                    <div class="card border-0 shadow-sm mb-3" style="border-radius: 8px; overflow: hidden; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">
                                        <div class="table-responsive">

                                            <table class="table table-sm align-middle mb-0 text-nowrap" style="font-size: 0.72rem; border-collapse: separate; border-spacing: 0;">

                                                {{-- Cabeceras de las Columnas --}}
                                                <thead class="text-uppercase text-secondary" style="font-size: 0.6rem; letter-spacing: 0.3px; background-color: #f8fafc;">
                                                    <tr>
                                                        <th class="ps-4 pe-3 py-3 border-bottom fw-medium text-start" style="width: 20%; border-color: #e2e8f0 !important;">Fecha & Canal</th>
                                                        <th class="px-3 py-3 border-bottom fw-medium text-start" style="width: 20%; border-color: #e2e8f0 !important;">Asesor / Agente</th>
                                                        <th class="px-3 py-3 border-bottom fw-medium text-start" style="width: 25%; border-color: #e2e8f0 !important;">Gestión & Resultado</th>
                                                        <th class="pe-4 ps-3 py-3 border-bottom fw-medium text-start" style="width: 35%; border-color: #e2e8f0 !important;">Notas / Observaciones</th>
                                                    </tr>
                                                </thead>

                                                <tbody class="border-top-0">
                                                    @foreach($interacciones as $interaccion)
                                                        <tr class="border-bottom" style="border-color: #f1f5f9 !important; transition: background-color 0.15s ease;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='transparent';">

                                                            {{-- 1. Fecha y Canal --}}
                                                            <td class="ps-4 pe-3 py-3">
                                                                <div class="d-flex flex-column gap-1">
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem; letter-spacing: 0.2px;">
                                                                        {{ $interaccion->interaction_date ? $interaccion->interaction_date->format('d M, Y - h:i A') : 'Sin fecha' }}
                                                                    </span>
                                                                    <div>
                                                                        <span class="badge rounded-pill d-inline-flex align-items-center gap-1" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; padding: 0.2rem 0.5rem; font-size: 0.6rem; font-weight: 400;">
                                                                            <i class="fas fa-link opacity-75"></i>
                                                                            {{ optional($interaccion->channel)->nombre ?? 'Canal Estándar' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            {{-- 2. Agente / Asesor --}}
                                                            <td class="px-3 py-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 28px; height: 28px; background-color: #f8fafc; border: 1px solid #e2e8f0; color: #64748b;">
                                                                        <i class="fas fa-headset opacity-75" style="font-size: 0.65rem;"></i>
                                                                    </div>
                                                                    <div class="d-flex flex-column">
                                                                        <span class="text-secondary fw-medium text-truncate" style="max-width: 140px; font-size: 0.75rem;" title="{{ optional($interaccion->agent)->name ?? 'No asignado' }}">
                                                                            {{ optional($interaccion->agent)->name ?? 'No asignado' }}
                                                                        </span>
                                                                        @if($interaccion->duration)
                                                                            <span class="text-muted d-flex align-items-center gap-1" style="font-size: 0.65rem;">
                                                                                <i class="far fa-clock opacity-50"></i>{{ $interaccion->duration }} min
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            {{-- 3. Tipo de Gestión y Resultado (Outcome) --}}
                                                            <td class="px-3 py-3">
                                                                <div class="d-flex flex-column gap-1 align-items-start">
                                                                    <span class="text-secondary fw-medium text-truncate" style="font-size: 0.75rem; max-width: 180px;" title="{{ optional($interaccion->type)->nombre ?? 'Gestión General' }}">
                                                                        {{ optional($interaccion->type)->nombre ?? 'Gestión General' }}
                                                                    </span>
                                                                    <span class="badge rounded-pill border d-inline-flex align-items-center gap-1 text-truncate"
                                                                        style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; padding: 0.2rem 0.5rem; font-size: 0.6rem; font-weight: 400; max-width: 180px;"
                                                                        title="{{ optional($interaccion->outcomeRelation)->nombre ?? 'Sin estado' }}">
                                                                        <i class="fas fa-check-double opacity-50"></i> {{ optional($interaccion->outcomeRelation)->nombre ?? 'Pendiente / Sin estado' }}
                                                                    </span>
                                                                </div>
                                                            </td>

                                                            {{-- 4. Notas (Con text-wrap para legibilidad) --}}
                                                            <td class="pe-4 ps-3 py-3 text-wrap" style="min-width: 250px;">
                                                                @if($interaccion->notes)
                                                                    <p class="mb-0 text-muted" style="font-size: 0.7rem; line-height: 1.5;">
                                                                        {{ $interaccion->notes }}
                                                                    </p>
                                                                @else
                                                                    <span class="text-muted opacity-50 fst-italic" style="font-size: 0.7rem;">Sin observaciones...</span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    {{-- Empty State Suave --}}
                                    <div class="text-center py-5 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-headset fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Sin Historial de Contacto</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">Aún no se han registrado interacciones, llamadas o seguimientos con este cliente.</p>
                                    </div>
                                @endif

                            </div>
                            {{-- ======================================================= --}}
                            {{-- FIN TAB 8 --}}
                            {{-- ======================================================= --}}

                            {{-- ======================================================= --}}
                            {{-- INICIO TAB 9: DOCUMENTOS (ECM Y FÍSICOS)                --}}
                            {{-- ======================================================= --}}
                            <div class="tab-pane fade" id="documentos" role="tabpanel" aria-labelledby="documentos-tab" tabindex="0">

                                {{-- HEADER DE LA SECCIÓN (Minimalista y Suave) --}}
                                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                    <div>
                                        <h5 class="fw-medium text-secondary mb-1" style="font-size: 1rem; letter-spacing: -0.2px;">Gestión Documental</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Estado de soportes de afiliación y ubicación en archivo ECM/Físico.</p>
                                    </div>
                                    @if(isset($documentosAsociado) && $documentosAsociado->ubicacion_ecm_link)
                                        {{-- Botón Estilo Píldora (Igual al "Agregar") --}}
                                        <a href="{{ $documentosAsociado->ubicacion_ecm_link }}"
                                        target="_blank"
                                        class="btn btn-light border rounded-pill px-3 py-0 text-muted shadow-none text-decoration-none d-inline-flex align-items-center"
                                        style="font-size: 0.7rem; height: 26px; transition: all 0.2s ease;"
                                        onmouseover="this.style.backgroundColor='#f0f9ff'; this.style.color='#0284c7'; this.style.borderColor='#bae6fd' !important;"
                                        onmouseout="this.style.backgroundColor='#f8fafc'; this.style.color='#6c757d'; this.style.borderColor='#cbd5e1' !important;">
                                            <i class="fas fa-cloud me-2 text-primary opacity-75"></i> Abrir ECM
                                        </a>
                                    @endif
                                </div>

                                @if(isset($documentosAsociado))
                                    <div class="row g-3 px-2">

                                        {{-- TARJETA 1: Checklist de Soportes --}}
                                        <div class="col-md-7">
                                            <div class="card border-0 shadow-sm h-100" style="border-radius: 8px; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">

                                                <div class="card-header bg-white border-bottom pt-3 pb-2 px-4" style="border-color: #f8fafc !important;">
                                                    <span class="fw-medium text-secondary" style="font-size: 0.8rem;">
                                                        <i class="fas fa-clipboard-check text-success opacity-75 me-2"></i> Soportes Radicados
                                                    </span>
                                                </div>

                                                <div class="card-body px-4 py-3">
                                                    <div class="row g-3">

                                                        {{-- Columna Izquierda Checklist --}}
                                                        <div class="col-sm-6">
                                                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                                @if($documentosAsociado->doc_formulario_afiliacion)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">Formulario Afiliación</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">Formulario Afiliación</span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                                @if($documentosAsociado->doc_autorizacion_datos)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">Autorización Datos</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">Autorización Datos</span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                                @if($documentosAsociado->doc_cedula_pastor)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">Cédula Asociado</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">Cédula Asociado</span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom border-sm-0" style="border-color: #f8fafc !important;">
                                                                @if($documentosAsociado->doc_licencia_pastoral)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">Licencia</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">Licencia</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        {{-- Columna Derecha Checklist --}}
                                                        <div class="col-sm-6">
                                                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                                @if($documentosAsociado->doc_cedula_esposa)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">Cédula Esposa</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">Cédula Esposa</span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                                @if($documentosAsociado->doc_registro_matrimonio)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">Reg. Matrimonio</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">Reg. Matrimonio</span>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 mb-0 pb-0">
                                                                @if($documentosAsociado->doc_id_hijos)
                                                                    <i class="fas fa-check-circle text-success opacity-75" style="font-size: 0.85rem;"></i>
                                                                    <span class="fw-medium text-secondary" style="font-size: 0.75rem;">IDs Hijos</span>
                                                                @else
                                                                    <i class="fas fa-circle text-muted opacity-25" style="font-size: 0.85rem;"></i>
                                                                    <span class="text-muted text-decoration-line-through opacity-75" style="font-size: 0.75rem;">IDs Hijos</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- TARJETA 2: Archivo Físico y Metadatos --}}
                                        <div class="col-md-5">
                                            <div class="card border-0 shadow-sm h-100" style="border-radius: 8px; background-color: #ffffff; border: 1px solid #f1f5f9 !important;">

                                                <div class="card-header bg-white border-bottom pt-3 pb-2 px-4" style="border-color: #f8fafc !important;">
                                                    <span class="fw-medium text-secondary" style="font-size: 0.8rem;">
                                                        <i class="fas fa-archive text-secondary opacity-75 me-2"></i> Archivo Físico
                                                    </span>
                                                </div>

                                                <div class="card-body px-4 py-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                        <span class="text-muted" style="font-size: 0.75rem;">Carpeta:</span>
                                                        <span class="fw-medium text-secondary" style="font-size: 0.75rem;">{{ $documentosAsociado->ubicacion_carpeta ?? 'N/A' }}</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                        <span class="text-muted" style="font-size: 0.75rem;">Caja:</span>
                                                        <span class="fw-medium text-secondary" style="font-size: 0.75rem;">{{ $documentosAsociado->numero_caja ?? 'N/A' }}</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="border-color: #f8fafc !important;">
                                                        <span class="text-muted" style="font-size: 0.75rem;">Folios:</span>
                                                        <span class="fw-medium text-secondary" style="font-size: 0.75rem;">{{ $documentosAsociado->cantidad_folios ?? '0' }}</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-muted" style="font-size: 0.75rem;">Digitalizado:</span>
                                                        @if($documentosAsociado->escaneado)
                                                            <span class="badge rounded-pill" style="background-color: #d1fae5; color: #047857; padding: 0.25rem 0.6rem; font-size: 0.65rem; font-weight: 500;">Sí</span>
                                                        @else
                                                            <span class="badge rounded-pill border" style="background-color: #f8fafc; color: #94a3b8; padding: 0.25rem 0.6rem; font-size: 0.65rem; font-weight: 400;">Pendiente</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                @else
                                    {{-- Empty State Suave (Al mismo estilo del ERP) --}}
                                    <div class="text-center py-5 rounded" style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                        <i class="fas fa-folder-open fs-3 text-secondary mb-3 opacity-25"></i>
                                        <h6 class="fw-medium text-secondary mb-1" style="font-size: 0.85rem;">Sin Expediente Documental</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">No se encontró una ficha de asociado vinculada a esta cédula.</p>
                                    </div>
                                @endif

                            </div>
                            {{-- ======================================================= --}}
                            {{-- FIN TAB 9 --}}
                            {{-- ======================================================= --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODALES GLOBALES (Tipo Certificado)        -->
    <!-- ========================================== -->
    <div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <!-- Formulario para generar el Certificado -->
            <form id="formGenerarCertificado" action="{{ route('certificados.operaciones.procesar_individual', $operacion->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-file-pdf text-danger me-2"></i> Generar Certificado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Envío del número de bloque oculto como referencia --}}
                    <input type="hidden" name="numero_bloque" value="{{ $operacion->numero_bloque ?? now()->format('YmdHis') }}">

                    {{-- Selección de Tipo de Certificado (Solución a estructura cortada) --}}
                    <div class="mb-3">
                        <label for="tipo_certificado_id" class="form-label text-muted fw-bold fs-8 text-uppercase">Tipo de Certificado a Generar</label>
                        <select name="tipo_certificado_id" id="tipo_certificado_id" class="form-select" required>
                            <option value="">Seleccione un tipo...</option>
                            @if(isset($tiposCertificados))
                                @foreach($tiposCertificados as $tipoCert)
                                    <option value="{{ $tipoCert->id }}">{{ $tipoCert->nombre }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text mt-2 fs-8 text-muted">
                            <i class="fas fa-info-circle text-primary"></i> Al generar un certificado, se evaluará la data del cliente procesada por el ETL en la base de datos.
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-bold shadow-sm" onclick="bloquearBotonUI(this, 'Generando...')">
                        <i class="fas fa-check me-2"></i> Generar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL: ASIGNAR NUEVA EXCEPCIÓN / CONFIGURACIÓN          --}}
    {{-- ======================================================= --}}
    <div class="modal fade" id="modalCrearConfiguracion" tabindex="-1" aria-labelledby="modalCrearConfiguracionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                {{-- HEADER DEL MODAL --}}
                <div class="modal-header bg-pastel-primary border-bottom-0 pb-3 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary" id="modalCrearConfiguracionLabel">
                        <i class="fas fa-tasks me-2"></i> Asignar Reglas de Excepción
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- FORMULARIO --}}
                <form action="{{ route('certificados.operaciones.config.individual') }}" method="POST">
                    @csrf

                    {{-- LLAVES FORÁNEAS OCULTAS --}}
                    <input type="hidden" name="id_operacion" value="{{ $operacion->id }}">

                    <div class="modal-body bg-light p-4">

                        {{-- ALERTA DE CONTEXTO --}}
                        <div class="alert bg-white border-info border-start border-4 shadow-sm py-2 px-3 mb-4 text-muted" style="font-size: 0.8rem;">
                            <i class="fas fa-info-circle text-info me-2"></i> Estás asignando reglas excepcionales al radicado <strong>{{ $operacion->numero_radicado }}</strong>.
                        </div>

                        <div class="row g-4">

                            {{-- COLUMNA IZQUIERDA: Notificaciones, Vigencia y Justificación --}}
                            <div class="col-md-5">

                                {{-- Switch de Notificaciones --}}
                                <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 border shadow-sm mb-3">
                                    <div><span class="fw-bold d-block text-dark" style="font-size: 0.85rem;"><i class="fas fa-bell text-warning me-1"></i> Notificaciones</span></div>
                                    <div class="form-check form-switch fs-5 m-0 p-0 d-flex align-items-center gap-2">
                                        <input type="hidden" name="estado_notificacion" value="0">
                                        <input class="form-check-input m-0 shadow-sm" type="checkbox" role="switch" name="estado_notificacion" value="1" checked style="cursor: pointer; width: 2.2em; height: 1.1em;">
                                    </div>
                                </div>

                                {{-- Vigencia y Justificación --}}
                                <div class="p-3 bg-white border rounded-3 shadow-sm">
                                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;"><i class="fas fa-calendar-alt text-primary me-1"></i> Vigente Hasta <span class="text-muted fw-normal">(Opcional)</span></label>
                                    <input type="date" class="form-control form-control-sm mb-3" name="vigente_hasta" min="{{ date('Y-m-d') }}">

                                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;"><i class="fas fa-comment-dots text-primary me-1"></i> Justificación <span class="text-danger fw-normal">*</span></label>
                                    <textarea class="form-control form-control-sm" name="justificacion" rows="3" required placeholder="Motivo por el cual se asignan estas reglas al cliente..."></textarea>
                                    <div class="form-text mt-1" style="font-size: 0.65rem;">Auditor: {{ auth()->user()->name ?? 'N/A' }}</div>
                                </div>

                            </div>

                            {{-- COLUMNA DERECHA: Selección Múltiple de Configuraciones --}}
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark mb-3"><i class="fas fa-list-check text-muted me-1"></i> Selecciona las reglas a aplicar (Múltiple):</label>

                                <div class="list-group custom-scrollbar shadow-sm bg-white" style="max-height: 400px; overflow-y: auto; border-radius: 12px; border: 1px solid #dee2e6;">
                                    @if(isset($configuracionesBase) && $configuracionesBase->count() > 0)
                                        @foreach($configuracionesBase as $cfg)
                                            @php
                                                // Corrección del JSON aplicada aquí también
                                                $p = is_array($cfg->parametros) ? $cfg->parametros : (json_decode($cfg->parametros, true) ?? []);
                                                $claseMora = strtolower($p['clasificacion_mora'] ?? 'n/a');
                                                $diasMax = $p['mora_dias_max'] ?? '0';

                                                $badgeColor = match($claseMora) {
                                                    'bueno' => 'bg-pastel-success text-success border-success',
                                                    'regular' => 'bg-pastel-info text-info border-info',
                                                    'atencion_especial' => 'bg-pastel-warning text-dark border-warning',
                                                    'restringido' => 'bg-pastel-danger text-danger border-danger',
                                                    'irregular' => 'bg-dark text-white border-dark',
                                                    default => 'bg-light text-secondary border-secondary'
                                                };
                                            @endphp
                                            <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0 border-bottom" style="cursor: pointer;">
                                                {{-- OJO AQUÍ: id_car_sia_config[] es un arreglo ahora --}}
                                                <input class="form-check-input flex-shrink-0 mt-0" type="checkbox" name="id_car_sia_config[]" value="{{ $cfg->id }}" style="font-size: 1.3rem;">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $cfg->accionVencimiento->nombre ?? 'Sin Acción Definida' }}</span>
                                                        <span class="badge bg-light text-dark border"><i class="fas fa-clock text-muted"></i> {{ $cfg->frecuencia_recordatorio_dias ?? 0 }} d</span>
                                                    </div>
                                                    <div class="d-flex gap-2 mt-1">
                                                        <span class="badge {{ $badgeColor }} border border-opacity-25" style="font-size: 0.7rem;">
                                                            {{ strtoupper(str_replace('_', ' ', $claseMora)) }}
                                                        </span>
                                                        <span class="badge bg-white text-secondary border" style="font-size: 0.7rem;">
                                                            Mora Max: {{ $diasMax }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5 bg-white border-0">
                                            <i class="fas fa-folder-open fs-2 text-muted opacity-25 mb-2"></i>
                                            <p class="text-muted m-0 fw-bold">No hay configuraciones base activas</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- FOOTER Y BOTONES --}}
                    <div class="modal-footer border-top-0 bg-white px-4 pb-4 pt-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Guardar Asignaciones
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </di

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



    @push('scripts')
    <script>
        $(document).ready(function() {
            const $modal = $('#modalEditarTercero');
            const $selects = $('.select2-buscador');

            // 1. Inicialización de Select2 dentro del modal
            $selects.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione o busque...',
                allowClear: true,
                dropdownParent: $modal
            });

            // Función para asignar valor de forma segura e insensible a espacios
            function setSelect2Flexible($element, targetValue) {
                if (!targetValue || targetValue.trim() === '') {
                    // Si la variable en JS viene vacía, intentamos rescatar la opción que Blade marcó como 'selected'
                    let bladeSelected = $element.find('option[selected]').val();
                    if (bladeSelected) {
                        $element.val(bladeSelected).trigger('change.select2');
                    }
                    return;
                }

                let cleanTarget = $.trim(targetValue);
                let matchingOption = null;

                // Buscamos cuál <option> coincide limpiando espacios en ambos lados
                $element.find('option').each(function() {
                    if ($.trim($(this).val()) === cleanTarget) {
                        matchingOption = $(this).val();
                        return false; // Romper loop
                    }
                });

                if (matchingOption !== null) {
                    $element.val(matchingOption).trigger('change.select2');
                } else {
                    console.warn("Select2: No se encontró opción coincidente para el valor:", cleanTarget);
                }
            }

            // 2. Evento al abrir el modal
            $modal.on('shown.bs.modal', function () {
                let distVal = "{{ trim($operacion->tercero->cod_dist ?? '') }}";
                let tipoVal = "{{ trim($operacion->tercero->tip_prv ?? '') }}";
                let congVal = "{{ trim($operacion->tercero->congrega ?? '') }}";

                setSelect2Flexible($('#select_cod_dist'), distVal);
                setSelect2Flexible($('#select_tip_prv'), tipoVal);
                setSelect2Flexible($('#select_congrega'), congVal);
            });
        });
    </script>
    @endpush

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

    <!-- ========================================== -->
    <!-- SCRIPTS PARA INTERACTIVIDAD DE LA TABLA    -->
    <!-- ========================================== -->
    <script>
        function toggleParametros(targetId, element) {
            // Obtenemos el <tr> que contiene el formulario oculto
            var targetRow = document.getElementById(targetId);

            // Obtenemos el ícono de la fila que acabamos de hacer clic
            var icon = element.querySelector('.icon-toggle');

            // Verificamos el estado actual para alternarlo
            if (targetRow.style.display === 'none' || targetRow.style.display === '') {
                // Es MUY importante usar 'table-row' y no 'block' para que no se rompa la tabla
                targetRow.style.display = 'table-row';

                // Cambiar ícono a flecha arriba
                if (icon) {
                    icon.classList.remove('fa-chevron-circle-down');
                    icon.classList.add('fa-chevron-circle-up');
                }
            } else {
                // Ocultar la fila
                targetRow.style.display = 'none';

                // Cambiar ícono a flecha abajo
                if (icon) {
                    icon.classList.remove('fa-chevron-circle-up');
                    icon.classList.add('fa-chevron-circle-down');
                }
            }
        }

        // ========================================== -->
        // FUNCIÓN PARA ACTUALIZAR ESTADO API DIRECTO  -->
        // ========================================== -->
        function actualizarEstadoApiDirecto(selectElement, idFactura, numeroBloque) {
            const nuevoEstado = selectElement.value;

            // Encontramos la fila (tr) actual de la tabla para manipular sus campos visualmente
            const fila = selectElement.closest('tr');

            // Cambiar visualmente el estilo de forma inmediata mientras responde el servidor
            if (nuevoEstado == '1') {
                selectElement.className = 'form-select form-select-sm border-0 shadow-none text-center fw-bold w-100 rounded-0 bg-transparent py-1 text-success';

                if (fila) {
                    // 1. Poner el campo de Mora en 0 automáticamente
                    const inputMora = fila.querySelector('input[name*="[dias_mora_automaticos]"]');
                    if (inputMora) {
                        inputMora.value = 0;
                        inputMora.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    // 2. Cambiar la Calificación a "Bueno" automáticamente
                    const selectCalificacion = fila.querySelector('select[name*="[calificacion]"]');
                    if (selectCalificacion) {
                        selectCalificacion.value = 'Bueno';
                        // Actualizamos su clase de color a verde (text-success)
                        selectCalificacion.className = 'form-select form-select-sm border-0 shadow-none bg-transparent px-1 text-center text-success';
                        selectCalificacion.dispatchEvent(new Event('change', { bubbles: true }));
                    }

                    // 3. Poner la fecha actual en Últ. Rec. (fecha_ultimo_recordatorio)
                    const inputUltRec = fila.querySelector('input[name*="[fecha_ultimo_recordatorio]"]');
                    if (inputUltRec) {
                        const hoy = new Date();
                        const anio = hoy.getFullYear();
                        const mes = String(hoy.getMonth() + 1).padStart(2, '0');
                        const dia = String(hoy.getDate()).padStart(2, '0');
                        inputUltRec.value = `${anio}-${mes}-${dia}`;
                    }

                    // 4. Construir la observación en el último campo de la fila
                    const inputObservacion = fila.querySelector('input[name*="[observacion]"]');
                    if (inputObservacion) {
                        const fechaActualFormateada = new Date().toLocaleDateString();
                        let textoActual = inputObservacion.value.trim();
                        const mensajePago = `Pago registrado el ${fechaActualFormateada}. Calificado como Bueno, mora ajustada a 0.`;

                        // Si ya tenía texto, lo acumulamos ordenadamente; si está vacío, colocamos el mensaje directo
                        if (textoActual !== '' && !textoActual.includes('Pago registrado')) {
                            inputObservacion.value = textoActual + " - " + mensajePago;
                        } else if (textoActual === '') {
                            inputObservacion.value = mensajePago;
                        }

                        // Actualizamos el title para que se visualice completo al pasar el mouse
                        inputObservacion.setAttribute('title', inputObservacion.value);
                    }
                }

            } else {
                selectElement.className = 'form-select form-select-sm border-0 shadow-none text-center fw-semibold w-100 rounded-0 bg-transparent py-1 text-muted';
            }

            // Construir la URL usando la ruta nombrada de Laravel
            const url = `{{ route('certificados.operaciones.estado_api.bloque', ['idFactura' => ':id', 'numeroBloque' => ':bloque']) }}`
                        .replace(':id', idFactura)
                        .replace(':bloque', numeroBloque);

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ estadoApi: nuevoEstado !== "" ? nuevoEstado : null })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Error al actualizar el estado API: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                alert('Ocurrió un error de red al intentar actualizar el estado API.');
            });
        }
    </script>

</x-base-layout>
