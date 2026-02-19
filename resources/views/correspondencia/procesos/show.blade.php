<x-base-layout>
    <div class="app-container py-4">
        {{-- Alerta de éxito --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                <i class="fas fa-check-circle me-3 fs-4"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Encabezado de la página --}}
        <header class="main-header mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('correspondencia.flujos.show', $proceso->flujo_id) }}" class="btn btn-white shadow-sm rounded-circle border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Volver al detalle">
                        <i class="fas fa-arrow-left text-muted"></i>
                    </a>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h1 class="h3 fw-bold mb-0">{{ $proceso->nombre }}</h1>
                            @if($proceso->activo)
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3" style="font-size: 0.7rem;">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> ACTIVO
                                </span>
                            @else
                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3" style="font-size: 0.7rem;">
                                    <i class="fas fa-pause me-1" style="font-size: 0.5rem;"></i> INACTIVO
                                </span>
                            @endif
                        </div>
                        <span class="badge bg-soft-primary mt-1" style="background-color: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                            <i class="fas fa-project-diagram me-1"></i> {{ $proceso->flujo->nombre }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('correspondencia.procesos.edit', $proceso) }}" class="btn btn-white border rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-edit me-2 text-warning"></i>Editar Proceso
                    </a>
                    <a href="{{ route('correspondencia.flujos.show', $proceso->flujo_id) }}" class="btn btn-outline-indigo rounded-pill px-3 fw-bold shadow-sm border-2">
                        <i class="bi bi-diagram-3 me-2"></i>Flujo Jerárquico Superior
                    </a>
                </div>
            </div>
        </header>

        <div class="row g-4">
            {{-- COLUMNA PRINCIPAL --}}
            <div class="col-lg-8">
                {{-- 1. DESCRIPCIÓN TÉCNICA --}}
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Descripción Técnica del Proceso
                    </h5> 
                    <p class="text-muted mb-4" style="line-height: 1.7;">
                        {{ $proceso->detalle ?: 'Sin descripción adicional para este paso del flujo.' }}
                    </p>
                    
                    <div class="row g-3 bg-light p-3 rounded-4">
                        <div class="col-md-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Configurado por</small>
                            <div class="fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-user-circle me-2 text-muted"></i>
                                {{ $proceso->creador->name ?? 'Sistema' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Fecha de Creación</small>
                            <div class="fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                {{ $proceso->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. NUEVO: CONFIGURACIÓN DE ARCHIVOS REQUERIDOS --}}
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px; border-left: 4px solid #4f46e5 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 d-flex align-items-center">
                            <i class="fas fa-file-signature me-2 text-indigo"></i> Configuración de Archivos Obligatorios
                        </h5>
                    </div>
                    
                    <form action="{{ route('correspondencia.procesos.update', $proceso) }}" method="POST">
                        @csrf
                        @method('PUT')
                        {{-- Campos ocultos para mantener la validación de update --}}
                        <input type="hidden" name="flujo_id" value="{{ $proceso->flujo_id }}">
                        <input type="hidden" name="nombre" value="{{ $proceso->nombre }}">
                        <input type="hidden" name="detalle" value="{{ $proceso->detalle }}">
                        <input type="hidden" name="activo" value="{{ $proceso->activo ? 1 : 0 }}">

                        <div class="row align-items-center mb-4 bg-soft-primary p-3 rounded-4 border" style="border-color: #c7d2fe !important;">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-indigo text-uppercase mb-1">¿Cuántos archivos se deben subir?</label>
                                <p class="small text-muted mb-2">Define la cantidad y asigna un nombre a cada documento requerido.</p>
                                <div class="input-group shadow-sm" style="width: 150px; border-radius: 10px; overflow: hidden;">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-sort-numeric-up text-indigo"></i></span>
                                    <input type="number" id="cantidad_archivos" name="numero_archivos" class="form-control border-start-0 fw-bold" min="0" value="{{ $proceso->numero_archivos ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <button type="submit" class="btn btn-indigo rounded-pill px-4 py-2 fw-bold shadow">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>

                        {{-- Contenedor donde aparecerán los inputs dinámicos --}}
                        <div id="contenedor_archivos" class="row g-3">
                            </div>
                    </form>
                </div>

                {{-- 3. TRÁMITES EN ESTE PASO (Antiguo Documentos en este paso) --}}
                <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 d-flex align-items-center text-secondary">
                            <i class="fas fa-envelope-open-text me-2"></i> Trámites activos en este paso
                        </h5>
                        <span class="badge bg-secondary text-white rounded-pill px-3">
                            {{ $proceso->flujo->correspondencias->where('proceso_actual_id', $proceso->id)->count() }} Activos
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 small fw-bold text-muted ps-3 text-uppercase">Radicado / Asunto</th>
                                    <th class="border-0 small fw-bold text-muted text-center text-uppercase">Gestión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proceso->flujo->correspondencias->where('proceso_actual_id', $proceso->id) as $corr)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-indigo mb-0">{{ $corr->nro_radicado }}</div>
                                            <div class="small text-muted">{{ Str::limit($corr->asunto, 60) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('correspondencia.correspondencias.show', $corr) }}" 
                                               class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 fw-bold text-primary">
                                                <i class="fas fa-eye me-1"></i> Ver Trámite
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5">
                                            <div class="opacity-25 mb-3">
                                                <i class="fas fa-folder-open fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted small">No hay correspondencias pendientes en esta etapa.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- COLUMNA LATERAL --}}
            <div class="col-lg-4">
                {{-- RESPONSABLES ACTIVOS --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 small text-uppercase text-muted">Responsables Activos</h5>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAsignar">
                            <i class="fas fa-plus me-1"></i> Añadir
                        </button>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="list-group list-group-flush">
                            @forelse($proceso->usuariosAsignados->where('activo', 1) as $asignacion)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm text-white rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 0.8rem;">
                                        {{ strtoupper(substr($asignacion->usuario->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $asignacion->usuario->name }}</div>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $asignacion->detalle ?: 'Integrante' }}</span>
                                    </div>
                                </div>
                                <form action="{{ route('correspondencia.procesos.removerUsuario', [$proceso->id, $asignacion->user_id]) }}" method="POST" id="form-remove-{{ $asignacion->user_id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-link text-danger p-0 border-0" 
                                            onclick="confirmarAccion('{{ $asignacion->usuario->name }}', 'form-remove-{{ $asignacion->user_id }}', '¿Desactivar usuario?')">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <div class="text-center py-4"><p class="text-muted m-0 small">Sin responsables activos.</p></div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- RESPONSABLES INACTIVOS (HISTORIAL) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background-color: #fcfcfc;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold m-0 small text-uppercase text-muted">Responsables Inactivos (Historial)</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="list-group list-group-flush">
                            @forelse($proceso->usuariosAsignados->where('activo', 0) as $asignacion)
                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center bg-transparent border-bottom opacity-75">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm text-secondary bg-light border rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" 
                                         style="width: 32px; height: 32px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($asignacion->usuario->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold">{{ $asignacion->usuario->name }}</div>
                                        <span class="text-muted" style="font-size: 0.65rem;">Desactivado el: {{ $asignacion->updated_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                                <form action="{{ route('correspondencia.procesos.asignarUsuario', $proceso) }}" method="POST" id="form-reactivar-{{ $asignacion->user_id }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $asignacion->user_id }}">
                                    <input type="hidden" name="detalle" value="{{ $asignacion->detalle }}">
                                    <button type="button" class="btn btn-link text-success p-0 border-0" 
                                            onclick="confirmarReactivacion('{{ $asignacion->usuario->name }}', 'form-reactivar-{{ $asignacion->user_id }}')">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <div class="text-center py-2"><p class="text-muted m-0" style="font-size: 0.7rem;">No hay registros inactivos.</p></div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ESTADOS PERMITIDOS --}}
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 small text-uppercase text-muted">Estados Permitidos</h5>
                        <button type="button" class="btn btn-indigo btn-sm rounded-pill px-3 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEstado">
                            <i class="fas fa-cog me-1"></i> Añadir
                        </button>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="list-group list-group-flush">
                            @forelse($proceso->estadosProcesos as $ep)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="icon-sm bg-soft-primary text-indigo rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-tag small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $ep->estado->nombre }}</div>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $ep->detalle ?: 'Sin observaciones' }}</span>
                                    </div>
                                </div>
                                <form action="{{ route('correspondencia.procesos.eliminarEstado', $ep->id) }}" method="POST" id="form-estado-{{ $ep->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-link text-danger p-0 border-0" 
                                            onclick="confirmarAccion('{{ $ep->estado->nombre }}', 'form-estado-{{ $ep->id }}', '¿Eliminar configuración?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <div class="text-center py-4"><p class="text-muted m-0 small">No hay estados configurados.</p></div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALES EXISTENTES (ASIGNAR, ESTADO, CONFIRMACIÓN, REACTIVACIÓN) --}}
    {{-- ... Aquí mantienes todos los modales HTML exactamente iguales a los que me enviaste ... --}}
    
    {{-- 1. MODAL ASIGNAR INTEGRANTE --}}
    <div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('correspondencia.procesos.asignarUsuario', $proceso) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Asignar integrante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Usuario Responsable</label>
                        <input list="usuariosDataList" id="user_search" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" placeholder="Escriba el nombre..." autocomplete="off">
                        <datalist id="usuariosDataList">
                            @foreach($usuarios_disponibles as $u)
                                <option data-id="{{ $u->id }}" value="{{ $u->name }}">
                            @endforeach
                        </datalist>
                        <input type="hidden" name="user_id" id="user_id_hidden" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Función o Rol</label>
                        <input type="text" name="detalle" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" placeholder="Ej: Revisor principal...">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow" style="background: #4f46e5;">Confirmar Asignación</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. MODAL VINCULAR ESTADO --}}
    <div class="modal fade" id="modalEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('correspondencia.procesos.guardarEstado', $proceso) }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                @csrf
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Vincular Estado Permitido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Estado del Catálogo</label>
                        <select name="id_estado" class="form-select border-0 bg-light p-3" style="border-radius: 12px;" required>
                            <option value="">Seleccione un estado...</option>
                            @foreach($estados_catalogo as $est)
                                <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Observaciones de Uso</label>
                        <textarea name="detalle" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" rows="2" placeholder="Describa para qué se usa este estado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-indigo rounded-pill px-4 fw-bold shadow text-white">Guardar Configuración</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. MODAL GLOBAL DE CONFIRMACIÓN (PELIGRO/DESACTIVAR) --}}
    <div class="modal fade" id="modalConfirmarGlobal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 60px; height: 60px;">
                            <i class="fas fa-exclamation-triangle fs-3"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark" id="confirm-title">¿Está seguro?</h5>
                    <p class="text-muted small mb-4" id="confirm-text"></p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="confirm-btn-submit" class="btn btn-danger w-100 rounded-pill fw-bold">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. MODAL DE REACTIVACIÓN (ÉXITO/RETORNAR) --}}
    <div class="modal fade" id="modalReactivarGlobal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 60px; height: 60px;">
                            <i class="fas fa-user-check fs-3"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark">Reactivar Usuario</h5>
                    <p class="text-muted small mb-4" id="reactivate-text"></p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" id="reactivate-btn-submit" class="btn btn-success w-100 rounded-pill fw-bold">Reactivar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- NUEVA LÓGICA: Generación Dinámica de Archivos Requeridos ---
            const inputCantidad = document.getElementById('cantidad_archivos');
            const contenedor = document.getElementById('contenedor_archivos');
            
            // Recuperamos los datos guardados si existen.
            let existingFiles = @json($proceso->tipos_archivos ?? []);
            if(typeof existingFiles === 'string') {
                try { existingFiles = JSON.parse(existingFiles); } catch(e) { existingFiles = []; }
            }
            if(!Array.isArray(existingFiles)) existingFiles = [];

            function renderArchivos(cantidad) {
                contenedor.innerHTML = '';
                for(let i = 0; i < cantidad; i++) {
                    let val = existingFiles[i] || '';
                    let col = document.createElement('div');
                    col.className = 'col-md-6 mb-3';
                    col.innerHTML = `
                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Nombre del Archivo ${i+1}</label>
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-file-alt text-indigo"></i></span>
                            <input type="text" name="tipos_archivos[]" class="form-control border-start-0" placeholder="Ej: Cédula, Contrato..." value="${val}" required>
                        </div>
                    `;
                    contenedor.appendChild(col);
                }
            }

            if(inputCantidad) {
                inputCantidad.addEventListener('input', function() {
                    let cant = parseInt(this.value) || 0;
                    if(cant < 0) { cant = 0; this.value = 0; }
                    renderArchivos(cant);
                });
                // Iniciar con el valor actual al cargar la página
                renderArchivos(parseInt(inputCantidad.value) || 0);
            }
            // ---------------------------------------------------------------

            // Lógica del buscador de usuarios
            const userSearch = document.getElementById('user_search');
            const userHidden = document.getElementById('user_id_hidden');
            const userDataList = document.getElementById('usuariosDataList');

            if(userSearch) {
                userSearch.addEventListener('input', function() {
                    const selectedOption = Array.from(userDataList.options).find(option => option.value === this.value);
                    userHidden.value = selectedOption ? selectedOption.getAttribute('data-id') : "";
                });
            }

            // Lógica del Modal de Confirmación Global (Desactivar)
            let currentFormId = null;
            const globalModalEl = document.getElementById('modalConfirmarGlobal');
            const bootstrapModal = new bootstrap.Modal(globalModalEl);

            window.confirmarAccion = function(nombre, formId, titulo) {
                currentFormId = formId;
                document.getElementById('confirm-title').innerText = titulo;
                document.getElementById('confirm-text').innerHTML = `Esta acción afectará a <strong>${nombre}</strong>. <br>¿Desea continuar?`;
                bootstrapModal.show();
            };

            document.getElementById('confirm-btn-submit').addEventListener('click', function() {
                if (currentFormId) document.getElementById(currentFormId).submit();
            });

            // Lógica del Modal de Reactivación
            let currentReactivateFormId = null;
            const reactivateModalEl = document.getElementById('modalReactivarGlobal');
            const reactivateModal = new bootstrap.Modal(reactivateModalEl);

            window.confirmarReactivacion = function(nombre, formId) {
                currentReactivateFormId = formId;
                document.getElementById('reactivate-text').innerHTML = `Vas a habilitar nuevamente a <strong>${nombre}</strong> como responsable en este proceso.`;
                reactivateModal.show();
            };

            document.getElementById('reactivate-btn-submit').addEventListener('click', function() {
                if (currentReactivateFormId) document.getElementById(currentReactivateFormId).submit();
            });
        });
    </script>

    <style>
        .text-indigo { color: #4f46e5 !important; }
        .bg-indigo { background-color: #4f46e5 !important; }
        .btn-indigo { background-color: #4f46e5; border: none; color: white; }
        .btn-indigo:hover { background-color: #4338ca; color: white; }
        .btn-outline-indigo { color: #4f46e5; border-color: #4f46e5; }
        .btn-outline-indigo:hover { background-color: #4f46e5; color: white; }
        .bg-soft-primary { background-color: #f0f4ff; color: #4f46e5; }
        .bg-success-subtle { background-color: #dcfce7; color: #166534; }
        .bg-danger-subtle { background-color: #fee2e2; color: #991b1b; }
        .table-hover tbody tr:hover { background-color: #f8faff; transition: 0.2s; }
        .opacity-75 { opacity: 0.75; }
    </style>
</x-base-layout>