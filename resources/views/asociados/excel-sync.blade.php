<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-sync-alt me-1"></i> Sincronización de Datos</span>
                <h1 class="main-title">Gestión Masiva y Estados de Asociados</h1>
                <p class="main-subtitle">
                    Administra cargas masivas en Excel y sincroniza el campo <strong>estado</strong> desde la maestra de terceros (<code>MaeTerceros</code>).
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.index') }}" class="btn-ghost-corporate me-2">
                    <i class="fas fa-arrow-left"></i> <span>Volver al Directorio</span>
                </a>
            </div>
        </header>

        {{-- Alertas del Sistema --}}
        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 border-start border-success border-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0 border-start border-danger border-4 mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-warning shadow-sm border-0 border-start border-warning border-4 mb-4">
                <h6 class="fw-bold mb-2"><i class="fas fa-times-circle me-2"></i>Errores detectados en el archivo:</h6>
                <ul class="mb-0 small" style="max-height: 140px; overflow-y: auto;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- =====================================================================
             BLOQUE A: SINCRONIZACIÓN MASIVA POR EXCEL (EXPORTAR / IMPORTAR)
             ===================================================================== --}}
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-file-excel text-success me-2"></i>Carga y Exportación Masiva por Excel
                    </h5>
                    <small class="text-muted">Exporta la base actual, edita registros y previsualiza cambios antes de confirmar.</small>
                </div>
            </div>

            <div class="row g-4">
                {{-- Paso 1: Descargar --}}
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-body p-4 d-flex flex-column text-center">
                            <div class="mb-3">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2">Paso 1</span>
                                <div>
                                    <i class="fas fa-file-download text-primary" style="font-size: 2.75rem; opacity: 0.85;"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Exportar Plantilla Oficial</h4>
                            <p class="text-muted small mb-4 px-3">
                                Descarga el archivo <code>.xlsx</code> con los expedientes actuales para realizar inserciones o actualizaciones masivas.
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('asociados.sincronizar.descargar') }}" data-no-loading class="btn-corporate-black w-100 justify-content-center">
                                    <i class="fas fa-download me-2"></i> <span>Descargar Base Actual (.xlsx)</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Paso 2: Subir --}}
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 h-100" style="background-color: #f8fafc;">
                        <div class="card-body p-4 d-flex flex-column text-center">
                            <div class="mb-3">
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 mb-2">Paso 2</span>
                                <div>
                                    <i class="fas fa-cloud-upload-alt text-success" style="font-size: 2.75rem; opacity: 0.85;"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Subir y Previsualizar Lote</h4>
                            <p class="text-muted small mb-4 px-3">
                                Sube el archivo modificado (<code>.xlsx</code>, <code>.xls</code> o <code>.csv</code>). Se validarán cédulas, duplicados y tipos de operación antes de guardar.
                            </p>

                            <form action="{{ route('asociados.sincronizar.subir') }}" method="POST" enctype="multipart/form-data" class="mt-auto text-start">
                                @csrf
                                <div class="input-group mb-3 shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="fas fa-file-excel"></i>
                                    </span>
                                    <input type="file" name="excel_file" class="form-control border-start-0" accept=".xlsx,.xls,.csv" required style="font-size: 0.85rem;">
                                </div>
                                <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color: var(--bs-success); font-weight: 600;">
                                    <i class="fas fa-cogs me-2"></i> Procesar y Validar Lote
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-secondary-subtle">

        {{-- =====================================================================
             BLOQUE B: VALIDACIÓN Y ACTUALIZACIÓN DE ESTADOS DESDE TERCEROS
             ===================================================================== --}}
        <div class="mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-3 py-1 mb-1">
                        <i class="fas fa-database me-1"></i> Fuente: MaeTerceros (cod_ter → estado)
                    </span>
                    <h5 class="fw-bold text-dark mb-0">
                        Validación y Actualización de Estados de Asociados
                    </h5>
                    <small class="text-muted">
                        Compara <code>mae_asociados.estado</code> frente a <code>MaeTerceros.estado</code> emparejando por <code>cedula = cod_ter</code>.
                    </small>
                </div>

                <form action="{{ route('asociados.sincronizar.validar-estados') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary fw-semibold shadow-sm">
                        <i class="fas fa-search me-1"></i> Auditar Diferencias de Estado
                    </button>
                </form>
            </div>

            {{-- Resumen rápido de discrepancias (si se ejecutó la auditoría) --}}
            @isset($resumenEstados)
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body py-3 px-4">
                                <div class="text-muted small">Con Diferencia de Estado</div>
                                <div class="fs-4 fw-bold text-warning">{{ $resumenEstados['diferencias'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body py-3 px-4">
                                <div class="text-muted small">Sin Coincidencia en Terceros</div>
                                <div class="fs-4 fw-bold text-secondary">{{ $resumenEstados['sin_tercero'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body py-3 px-4">
                                <div class="text-muted small">Ya Sincronizados</div>
                                <div class="fs-4 fw-bold text-success">{{ $resumenEstados['iguales'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('asociados.sincronizar.actualizar-estados') }}" method="POST" id="formActualizarEstados">
                        @csrf

                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark">Alcance de actualización</label>
                                <select name="alcance" class="form-select form-select-sm" required>
                                    <option value="diferencias" selected>Solo registros donde el estado difiere de MaeTerceros</option>
                                    <option value="todos">Todos los asociados que existan en MaeTerceros</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark">Mapeo de códigos cortos (MaeTerceros.estado)</label>
                                <div class="form-control form-control-sm bg-light text-muted">
                                    <code>A / ACTIVO / 1</code> → <strong>Activo</strong> &nbsp;|&nbsp;
                                    <code>I / INACTIVO / 0</code> → <strong>Inactivo</strong>
                                </div>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="button" class="btn btn-primary btn-sm fw-semibold shadow-sm py-2" id="btnAbrirModalEstados">
                                    <i class="fas fa-check-double me-1"></i> Aplicar Estados
                                </button>
                            </div>
                        </div>

                        {{-- Tabla de previsualización de diferencias cuando existan --}}
                        @if(!empty($diferenciasEstado) && count($diferenciasEstado) > 0)
                            <div class="table-responsive mt-4 border rounded-3" style="max-height: 360px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Asociado</th>
                                            <th>Estado Actual (Asociado)</th>
                                            <th>Estado en Terceros (Raw)</th>
                                            <th>Nuevo Estado Propuesto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($diferenciasEstado as $row)
                                            <tr>
                                                <td class="fw-semibold">{{ $row['cedula'] }}</td>
                                                <td>{{ $row['nombre'] }}</td>
                                                <td><span class="badge bg-secondary-subtle text-dark">{{ $row['estado_actual'] ?? 'Sin estado' }}</span></td>
                                                <td><code>{{ $row['estado_tercero_raw'] ?? 'NULL' }}</code></td>
                                                <td><span class="badge bg-primary">{{ $row['estado_normalizado'] }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif(isset($diferenciasEstado))
                            <div class="alert alert-light border text-muted small mb-0 mt-3">
                                <i class="fas fa-info-circle me-1"></i> No se encontraron diferencias de estado entre los asociados y <code>MaeTerceros</code>.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         MODAL: CONFIRMACIÓN DE ASOCIADOS A EDITAR POR ESTADO
         ===================================================================== --}}
    <div class="modal fade" id="modalConfirmarEstados" tabindex="-1" aria-labelledby="modalConfirmarEstadosLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold text-dark" id="modalConfirmarEstadosLabel">
                        <i class="fas fa-user-check text-primary me-2"></i>Confirmar Actualización de Estados
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3">
                        Se sincronizará el campo <strong>estado</strong> de los expedientes desde <code>MaeTerceros</code> según el alcance seleccionado:
                    </p>

                    <div class="p-3 rounded-3 bg-light border text-center mb-3">
                        <div class="text-muted small text-uppercase fw-semibold">Expedientes que serán editados</div>
                        <div class="display-6 fw-bold text-primary my-1" id="cantidadAsociadosEditar">
                            {{ $conteoEstados['diferencias'] ?? (isset($diferenciasEstado) ? count($diferenciasEstado) : 0) }}
                        </div>
                        <div class="small text-muted" id="descripcionAlcanceModal">
                            Solo registros con diferencia frente a MaeTerceros
                        </div>
                    </div>

                    <div class="alert alert-warning py-2 px-3 small mb-0 border-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Esta acción actualizará el estado en <code>mae_asociados</code> sin disparar cambios adicionales en <code>MaeTerceros</code>.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-2">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary btn-sm fw-semibold px-4" id="btnConfirmarEnvioEstados">
                        <i class="fas fa-check me-1"></i> Sí, Actualizar Estados
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formActualizarEstados');
        const selectAlcance = form ? form.querySelector('select[name="alcance"]') : null;
        const btnAbrir = document.getElementById('btnAbrirModalEstados');
        const btnConfirmar = document.getElementById('btnConfirmarEnvioEstados');
        const modalEl = document.getElementById('modalConfirmarEstados');
        const countEl = document.getElementById('cantidadAsociadosEditar');
        const descEl = document.getElementById('descripcionAlcanceModal');

        const conteos = {
            diferencias: {{ (int) ($conteoEstados['diferencias'] ?? (isset($diferenciasEstado) ? count($diferenciasEstado) : 0)) }},
            todos: {{ (int) ($conteoEstados['todos'] ?? 0) }}
        };

        if (!form || !btnAbrir || !modalEl) return;

        function actualizarTextoModal() {
            const alcance = selectAlcance ? selectAlcance.value : 'diferencias';
            if (alcance === 'todos') {
                countEl.textContent = conteos.todos;
                descEl.textContent = 'Todos los asociados con estado válido en MaeTerceros';
            } else {
                countEl.textContent = conteos.diferencias;
                descEl.textContent = 'Solo registros con diferencia frente a MaeTerceros';
            }
        }

        if (selectAlcance) {
            selectAlcance.addEventListener('change', actualizarTextoModal);
        }

        btnAbrir.addEventListener('click', function () {
            actualizarTextoModal();
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                modalInstance.show();
            } else {
                form.submit();
            }
        });

        if (btnConfirmar) {
            btnConfirmar.addEventListener('click', function () {
                btnConfirmar.disabled = true;
                form.submit();
            });
        }
    });
    </script>

    @include('asociados.partials.styles')
</x-base-layout>
