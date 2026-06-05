<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-shield-alt me-1"></i> Validación de Seguridad</span>
                <h1 class="main-title">Mesa de Confirmación de Datos</h1>
                <p class="main-subtitle">Previsualización del lote analizado. Revisa y corrige los conflictos antes de guardar.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.sincronizar.index') }}" class="btn-ghost-corporate me-2 text-danger border-danger">
                    <i class="fas fa-times"></i> <span>Cancelar Lote</span>
                </a>
                <button type="button" onclick="document.getElementById('form-confirmar-lote').submit();" class="btn-corporate-black shadow-sm transition-all" style="background-color: #198754; color: white;">
                    <i class="fas fa-check-double"></i> <span>Aplicar Cambios a la BD</span>
                </button>
            </div>
        </header>

        {{-- ALERTA DE REGISTROS EXPULSADOS POR EL GUARDIA --}}
        @if(isset($errores) && count($errores) > 0)
            <div class="alert alert-warning shadow-sm border-0 border-start border-warning border-4 mb-4">
                <h6 class="fw-bold mb-2 text-dark"><i class="fas fa-trash-alt text-warning me-2"></i>Registros omitidos por seguridad ({{ count($errores) }} encontrados):</h6>
                <p class="small text-muted mb-2">Estos registros no pasaron a la mesa de confirmación porque no tienen cédula válida o están duplicados en tu archivo Excel.</p>
                <ul class="mb-0 small text-dark" style="max-height: 120px; overflow-y: auto;">
                    @foreach($errores as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CÁLCULO PREVIO DE CONFLICTOS --}}
        @php
            $cantidadConflictos = collect($filasProcesadas)->filter(function($fila) {
                return empty($fila['cedula']) || empty($fila['nombre1']) || empty($fila['apellido1']);
            })->count();
        @endphp

        {{-- Contadores y Botón de Filtro --}}
        <div class="row g-4 mb-4 align-items-center">
            <div class="col-md-4">
                <div class="dash-widget border-bottom-success shadow-sm h-100">
                    <div class="widget-icon bg-success-light text-success"><i class="fas fa-user-plus"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">Nuevos a Crear</span>
                        <h3 class="widget-number">{{ $contadorNuevos }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dash-widget border-bottom-warning shadow-sm h-100">
                    <div class="widget-icon bg-warning-light text-warning"><i class="fas fa-user-edit"></i></div>
                    <div class="widget-details w-100">
                        <span class="widget-title">A Actualizar</span>
                        <h3 class="widget-number">{{ $contadorActualizaciones }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 border-start {{ $cantidadConflictos > 0 ? 'border-danger' : 'border-success' }} border-4 h-100 bg-light">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <span class="fw-bold {{ $cantidadConflictos > 0 ? 'text-danger' : 'text-success' }} mb-2">
                            @if($cantidadConflictos > 0)
                                <i class="fas fa-exclamation-circle me-1"></i> Conflictos detectados: {{ $cantidadConflictos }}
                            @else
                                <i class="fas fa-check-circle me-1"></i> Lote sin conflictos
                            @endif
                        </span>
                        
                        {{-- BOTÓN MÁGICO DE FILTRO (Se desactiva si no hay errores) --}}
                        <button type="button" id="btnToggleConflictos" 
                                class="btn {{ $cantidadConflictos > 0 ? 'btn-outline-danger' : 'btn-outline-secondary disabled' }} btn-sm w-100 fw-bold" 
                                onclick="toggleConflictos()" 
                                {{ $cantidadConflictos == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-filter"></i> Mostrar Solo Conflictos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="form-confirmar-lote" action="{{ route('asociados.sincronizar.confirmar') }}" method="POST">
            @csrf
            
            <div class="card shadow-sm border-0 mb-4 rounded-3 border-start border-primary border-4">
                <div class="card-body p-3 bg-white d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="w-50">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-sitemap text-primary me-2"></i>Sincronización Transversal Automática</h6>
                        <p class="text-muted small mb-0">Define si los cambios de este lote deben afectar también la tabla maestra global de Terceros.</p>
                    </div>
                    <div>
                        <select name="sincronizar_terceros_global" class="form-select form-select-sm text-dark fw-bold border-primary shadow-sm" style="min-width: 300px;">
                            <option value="1" selected>Sí, sincronizar con MaeTerceros</option>
                            <option value="0">No, guardar únicamente en módulo Asociados</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="show-card-corp shadow-sm">
                <div class="show-body p-0">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle m-0 ecm-table" id="tabla-previa">
                            <thead class="table-light text-uppercase sticky-top shadow-sm" style="font-size: 0.75rem; z-index: 10;">
                                <tr>
                                    <th class="ps-4 text-center" style="width: 8%;">Fila Excel</th>
                                    <th style="width: 12%;">Estado</th>
                                    <th style="width: 15%;">Cédula</th>
                                    <th style="width: 30%;">Nombres y Apellidos</th>
                                    <th style="width: 20%;">Correo Electrónico</th>
                                    <th class="pe-4" style="width: 15%;">Distrito</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filasProcesadas as $index => $fila)
                                    {{-- Lógica para detectar si esta fila tiene algún campo vacío crítico --}}
                                    @php
                                        $tieneConflicto = empty($fila['cedula']) || empty($fila['nombre1']) || empty($fila['apellido1']);
                                    @endphp

                                    <tr class="{{ $tieneConflicto ? 'table-danger fila-conflicto border-bottom border-danger border-opacity-25' : 'fila-ok' }}">
                                        <td class="ps-4 text-center">
                                            <span class="badge {{ $tieneConflicto ? 'bg-danger' : 'bg-light text-secondary' }} border font-monospace shadow-sm">
                                                #{{ $fila['linea'] }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            @if($tieneConflicto)
                                                <span class="badge bg-danger text-white shadow-sm"><i class="fas fa-exclamation-triangle me-1"></i> ERROR</span>
                                            @elseif($fila['accion'] == 'CREATE')
                                                <span class="badge bg-success-light text-success border border-success"><i class="fas fa-plus me-1"></i> NUEVO</span>
                                            @else
                                                <span class="badge bg-warning-light text-warning border border-warning text-dark"><i class="fas fa-pen me-1"></i> ACTUALIZAR</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($tieneConflicto)
                                                {{-- INPUT PARA CORREGIR CÉDULA --}}
                                                <div class="input-group input-group-sm shadow-sm">
                                                    <span class="input-group-text bg-white border-danger text-danger"><i class="fas fa-id-card"></i></span>
                                                    <input type="text" name="correcciones[{{ $index }}][cedula]" class="form-control border-danger text-danger fw-bold" value="{{ $fila['cedula'] ?? '' }}" placeholder="Requerido">
                                                </div>
                                                <input type="hidden" name="correcciones[{{ $index }}][linea]" value="{{ $fila['linea'] }}">
                                            @else
                                                <span class="fw-bold text-dark"><i class="far fa-id-card text-muted me-1"></i> {{ $fila['cedula'] }}</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($tieneConflicto)
                                                {{-- INPUTS PARA CORREGIR NOMBRES --}}
                                                <div class="input-group input-group-sm shadow-sm">
                                                    <input type="text" name="correcciones[{{ $index }}][nombre1]" class="form-control border-danger" value="{{ $fila['nombre1'] ?? '' }}" placeholder="1er Nombre">
                                                    <input type="text" name="correcciones[{{ $index }}][apellido1]" class="form-control border-danger" value="{{ $fila['apellido1'] ?? '' }}" placeholder="1er Apellido">
                                                </div>
                                            @else
                                                <span class="d-block fw-bold" style="font-size: 0.85rem;">
                                                    {{ trim(($fila['nombre1'] ?? '') . ' ' . ($fila['nombre2'] ?? '') . ' ' . ($fila['apellido1'] ?? '') . ' ' . ($fila['apellido2'] ?? '')) }}
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <div class="text-truncate text-muted small" style="max-width: 180px;">
                                                @if(!empty($fila['correo_pastor']))
                                                    <i class="fas fa-envelope text-primary me-1"></i> {{ $fila['correo_pastor'] }}
                                                @else
                                                    <span class="text-secondary opacity-75"><i class="fas fa-minus"></i> No registra</span>
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
                
                {{-- FOOTER INFORMATIVO --}}
                <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold">
                        <i class="fas fa-eye text-info me-1"></i> Total en lote: {{ count($filasProcesadas) }} registros.
                    </span>
                    @if($cantidadConflictos > 0)
                        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-tools me-1"></i> Faltan {{ $cantidadConflictos }} por corregir</span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-check me-1"></i> Lote listo para procesar</span>
                    @endif
                </div>
            </div>
        </form>
    </div>
    @include('asociados.partials.styles')

    {{-- SCRIPT PARA EL FILTRO VISUAL --}}
    <script>
        let mostrandoSoloConflictos = false;

        function toggleConflictos() {
            const btnToggle = document.getElementById('btnToggleConflictos');
            // Verificamos si el botón está desactivado
            if(btnToggle.hasAttribute('disabled')) return; 

            mostrandoSoloConflictos = !mostrandoSoloConflictos;
            const filasOk = document.querySelectorAll('.fila-ok');

            if (mostrandoSoloConflictos) {
                // Ocultar las filas buenas, dejar solo los conflictos
                filasOk.forEach(fila => fila.style.display = 'none');
                btnToggle.innerHTML = '<i class="fas fa-list"></i> Mostrar Todos los Registros';
                btnToggle.classList.replace('btn-outline-danger', 'btn-danger');
            } else {
                // Mostrar todo de nuevo
                filasOk.forEach(fila => fila.style.display = '');
                btnToggle.innerHTML = '<i class="fas fa-filter"></i> Mostrar Solo Conflictos';
                btnToggle.classList.replace('btn-danger', 'btn-outline-danger');
            }
        }
    </script>
</x-base-layout>