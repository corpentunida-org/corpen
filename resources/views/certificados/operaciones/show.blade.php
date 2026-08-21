<x-base-layout>
    <style>
        /* Mismos estilos pasteles omitidos aquí por brevedad, asegúrate de incluirlos o tenerlos globales */
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        
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
    </style>

    <div class="app-container py-4">
        
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <a href="{{ route('certificados.operaciones.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block fw-semibold">
                    <i class="fas fa-arrow-left me-1"></i> Volver a la matriz
                </a>
                <h1 class="h3 fw-bold m-0" style="color: #2c3e50;">
                    Operación <span class="text-primary">{{ $operacion->numero_radicado }}</span>
                </h1>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning shadow-sm rounded-pill px-4 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalTransicionar">
                    <i class="fas fa-exchange-alt me-2"></i> Cambiar Estado
                </button>
                <button type="button" class="btn btn-info shadow-sm rounded-pill px-4 fw-bold text-white" data-bs-toggle="modal" data-bs-target="#modalAlerta">
                    <i class="fas fa-bell me-2"></i> Programar Alerta
                </button>
            </div>
        </div>

        <div class="row g-4">
            {{-- COLUMNA IZQUIERDA: Resumen --}}
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
                                <div class="fw-bolder text-dark">{{ $operacion->tercero->nom_ter }} {{ $operacion->tercero->apl1 }}</div>
                                <div class="text-muted fs-8">NIT: {{ $operacion->tercero->cod_ter }}</div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-7">
                                <span class="text-muted"><i class="fas fa-phone-alt me-2 opacity-50"></i>Teléfono:</span>
                                <span class="fw-semibold text-dark">{{ $operacion->tercero->tel ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 fs-7">
                                <span class="text-muted"><i class="fas fa-envelope me-2 opacity-50"></i>Email:</span>
                                <span class="fw-semibold text-dark">{{ $operacion->tercero->email ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between fs-7">
                                <span class="text-muted"><i class="fas fa-map-marker-alt me-2 opacity-50"></i>Ciudad:</span>
                                <span class="fw-semibold text-dark">{{ $operacion->tercero->ciudad ?? 'N/A' }}</span>
                            </div>
                        @else
                            <div class="alert bg-pastel-warning text-center border-0 rounded-4">Tercero no encontrado.</div>
                        @endif
                    </div>
                </div>

                <div class="card card-custom shadow-sm border-0">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase fw-bold text-muted mb-3 fs-8"><i class="fas fa-microchip me-2"></i> Info Técnica</h6>
                        <div class="d-flex justify-content-between mb-2 fs-7">
                            <span class="text-muted">Bloque:</span>
                            <span class="fw-bold text-primary">{{ $operacion->numero_bloque }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-7">
                            <span class="text-muted">ID Factura Staging:</span>
                            <span class="fw-semibold text-dark">{{ $operacion->id_factura ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: Pestañas --}}
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
                                    <i class="fas fa-history me-2"></i> Historial
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="tab-content" id="operacionTabsContent">
                            
                            {{-- TAB 1: Líneas --}}
                            <div class="tab-pane fade show active" id="lineas" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="text-muted fs-8 text-uppercase bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 border-0 rounded-start">Línea de Crédito</th>
                                                <th class="py-3 border-0">Vencimiento</th>
                                                <th class="py-3 border-0 text-end pe-4 rounded-end">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($operacion->lineas as $linea)
                                                <tr>
                                                    <td class="ps-4 fw-bold text-dark border-bottom py-3">
                                                        <i class="fas fa-box-open text-muted me-2 opacity-50"></i>
                                                        {{ $linea->lineaCredito->nombre ?? 'Línea Desconocida' }}
                                                    </td>
                                                    <td class="border-bottom text-muted">{{ $linea->fecha_venci ? $linea->fecha_venci->format('d/m/Y') : 'N/A' }}</td>
                                                    <td class="text-end pe-4 border-bottom">
                                                        @if($linea->estadoOperacion && $linea->estadoOperacion->estado)
                                                            <span class="badge bg-pastel-primary rounded-pill px-3 py-2">{{ $linea->estadoOperacion->estado->nombre }}</span>
                                                        @else
                                                            <span class="badge bg-pastel-secondary rounded-pill px-3 py-2 text-dark">Pendiente</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center py-5 text-muted">No hay líneas registradas.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- TAB 2 y 3: Aquí irían las tablas de alertas e historial adaptadas al mismo estilo limpio --}}
                            {{-- (Omitidos por brevedad visual, pero siguen la misma estructura de la tabla de arriba) --}}
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Los Modales se mantienen igual, solo cambiando `bi bi-` por `fas fa-` --}}
</x-base-layout>