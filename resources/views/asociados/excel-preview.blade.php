<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-shield-alt me-1"></i> Validación de Seguridad</span>
                <h1 class="main-title">Mesa de Confirmación de Datos</h1>
                <p class="main-subtitle">Previsualización del lote analizado. Ningún registro ha sido guardado permanentemente en el sistema todavía.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.sincronizar.index') }}" class="btn-ghost-corporate me-2 text-danger border-danger">
                    <i class="fas fa-times"></i> <span>Cancelar Lote</span>
                </a>
                <button type="button" onclick="document.getElementById('form-confirmar-lote').submit();" class="btn-corporate-black" style="background-color: #198754; color: white;">
                    <i class="fas fa-check-double"></i> <span>Aplicar Cambios a la BD</span>
                </button>
            </div>
        </header>

        {{-- Widgets de Contadores --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="dash-widget border-bottom-success shadow-sm">
                    <div class="widget-icon bg-success-light text-success"><i class="fas fa-user-plus"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Nuevos Registros a Crear</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $contadorNuevos }}</h3>
                            <span class="badge bg-success shadow-sm">Nuevos</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dash-widget border-bottom-warning shadow-sm">
                    <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-user-edit"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Expedientes a Actualizar</span>
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <h3 class="widget-number">{{ $contadorActualizaciones }}</h3>
                            <span class="badge bg-warning text-dark shadow-sm">Sobreescritura</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="form-confirmar-lote" action="{{ route('asociados.sincronizar.confirmar') }}" method="POST">
            @csrf
            
            {{-- Control de Negocio Transversal --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3 border-start border-primary border-4">
                <div class="card-body p-3 bg-white d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="w-50">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-sitemap text-primary me-2"></i>Sincronización Transversal Automática</h6>
                        <p class="text-muted small mb-0">Define si los cambios de este lote deben afectar también la tabla maestra global de Terceros.</p>
                    </div>
                    <div>
                        <select name="sincronizar_terceros_global" class="form-select form-select-sm text-dark fw-bold border-primary shadow-sm" style="min-width: 300px; font-size: 0.8rem;">
                            <option value="1" selected>Sí, sincronizar con MaeTerceros (Recomendado)</option>
                            <option value="0">No, guardar únicamente en módulo Asociados</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabla Previsualización (Estilo ECM) --}}
            <div class="show-card-corp shadow-sm">
                <div class="show-body p-0">
                    <div class="table-responsive" style="max-height: 500px;">
                        <table class="table table-hover align-middle m-0 ecm-table">
                            <thead class="table-light text-uppercase sticky-top shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 text-center" style="width: 8%;">Fila Excel</th>
                                    <th style="width: 12%;">Operación</th>
                                    <th style="width: 15%;">Cédula</th>
                                    <th style="width: 25%;">Nombre Completo Calculado</th>
                                    <th style="width: 20%;">Correo Electrónico</th>
                                    <th class="pe-4" style="width: 20%;">Distrito / Zona</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filasProcesadas as $fila)
                                    <tr>
                                        <td class="ps-4 text-center">
                                            <span class="badge bg-light text-secondary border font-monospace">#{{ $fila['linea'] }}</span>
                                        </td>
                                        
                                        <td>
                                            @if($fila['accion'] == 'CREATE')
                                                <span class="badge bg-success-light text-success border border-success"><i class="fas fa-plus me-1"></i> NUEVO</span>
                                            @else
                                                <span class="badge bg-warning-light text-warning border border-warning text-dark"><i class="fas fa-pen me-1"></i> ACTUALIZAR</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="fw-bold text-dark"><i class="far fa-id-card text-muted me-1"></i> {{ $fila['cedula'] }}</span>
                                        </td>
                                        
                                        <td>
                                            <span class="d-block fw-bold" style="font-size: 0.85rem;">
                                                {{ trim(($fila['nombre1'] ?? '') . ' ' . ($fila['nombre2'] ?? '') . ' ' . ($fila['apellido1'] ?? '') . ' ' . ($fila['apellido2'] ?? '')) }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <div class="text-truncate text-muted small" style="max-width: 180px;">
                                                @if(!empty($fila['correo_pastor']))
                                                    <i class="fas fa-envelope text-primary me-1"></i> {{ $fila['correo_pastor'] }}
                                                @else
                                                    <span class="text-danger"><i class="fas fa-times-circle"></i> No registra</span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td class="pe-4">
                                            <span class="text-muted small">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $fila['distrito_actual'] ?? '---' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="fas fa-eye text-info me-1"></i> Previsualizando {{ count($filasProcesadas) }} registros del lote analizado.</span>
                </div>
            </div>
        </form>
    </div>
    @include('asociados.partials.styles')
</x-base-layout>