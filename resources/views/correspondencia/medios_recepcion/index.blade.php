<x-base-layout>
    {{-- ESTILOS PERSONALIZADOS PARA ESTA VISTA --}}
    <style>
        :root {
            --primary-soft: #eff6ff;
            --primary-dark: #1e40af;
        }
        .avatar-initial {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border-radius: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .table-row-hover:hover {
            background-color: #f8fafc;
        }
        .table-row-hover:hover .avatar-initial {
            transform: scale(1.1);
            border-radius: 50%; /* Se vuelve redondo al pasar el mouse */
        }
        .status-dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        .status-active { background-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
        .status-inactive { background-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2); }
        
        /* Modal Glassy Header */
        .modal-header-custom {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid #e2e8f0;
        }
    </style>

    <div class="app-container py-5 px-4">
        
        {{-- HEADER CON BUSCADOR Y ACCIONES --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h1 class="h3 fw-bolder text-dark mb-1">
                    <i class="bi bi-inbox-fill text-primary me-2"></i>Medios de Recepción
                </h1>
                <p class="text-muted small mb-0 ms-1">Gestión de canales de entrada para correspondencia.</p>
            </div>
            
            <div class="d-flex gap-2 w-100 w-md-auto">
                <div class="input-group shadow-sm" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0 ps-3 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Buscar medio..." aria-label="Buscar">
                </div>
                
                <button class="btn btn-primary shadow hover-lift px-4 fw-semibold" onclick="nuevoMedio()">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo
                </button>
            </div>
        </div>

        {{-- CONTENEDOR PRINCIPAL --}}
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-uppercase small fw-bold text-muted">
                            <tr style="height: 50px;">
                                <th class="ps-4" style="width: 45%;">Medio / Canal</th>
                                <th style="width: 35%;">Descripción</th>
                                <th style="width: 10%;">Estado</th>
                                <th class="text-end pe-4" style="width: 10%;">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($medios as $medio)
                            @php
                                // Colores aleatorios para el avatar basados en el ID
                                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-indigo'];
                                $colorClass = $colors[$medio->id % count($colors)];
                                $initial = substr($medio->nombre, 0, 1);
                            @endphp
                            <tr class="table-row-hover transition-all border-bottom">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar con Inicial --}}
                                        <div class="avatar-initial {{ $colorClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $colorClass) }} me-3">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $medio->nombre }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">ID: #{{ str_pad($medio->id, 3, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary">
                                    @if($medio->descripcion)
                                        {{ Str::limit($medio->descripcion, 50) }}
                                    @else
                                        <span class="text-muted fst-italic opacity-50">Sin descripción</span>
                                    @endif
                                </td>
                                <td>
                                    @if($medio->activo)
                                        <div class="d-flex align-items-center">
                                            <span class="status-dot status-active"></span>
                                            <span class="fw-semibold text-success small">Activo</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="status-dot status-inactive"></span>
                                            <span class="fw-semibold text-danger small">Inactivo</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-light btn-sm rounded-3 text-primary border hover-lift" 
                                            onclick="editMedio({{ $medio->toJson() }})"
                                            data-bs-toggle="tooltip" title="Editar detalles">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            {{-- ESTADO VACÍO (EMPTY STATE) --}}
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="py-4">
                                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                            <i class="bi bi-inbox display-4 text-muted opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-muted">No hay medios registrados</h5>
                                        <p class="text-muted small mb-3">Comienza creando el primer canal de recepción.</p>
                                        <button class="btn btn-outline-primary btn-sm rounded-pill px-4" onclick="nuevoMedio()">
                                            Crear ahora
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- PAGINACIÓN --}}
            @if($medios->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end">
                {{ $medios->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL MODERNO --}}
    <div class="modal fade" id="modalMedio" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="formMedio" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    {{-- CABECERA MODAL --}}
                    <div class="modal-header modal-header-custom p-4 rounded-top-4">
                        <div>
                            <h5 class="modal-title fw-bold text-dark" id="modalTitle">Nuevo Registro</h5>
                            <p class="mb-0 small text-muted">Complete la información del canal.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    {{-- CUERPO MODAL --}}
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Nombre del Medio <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="bi bi-tag"></i></span>
                                    <input type="text" name="nombre" id="inputNombre" class="form-control form-control-lg bg-light border-start-0" 
                                           placeholder="Ej: Ventanilla Única" required style="font-size: 1rem;">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Descripción</label>
                                <textarea name="descripcion" id="inputDescripcion" class="form-control bg-light" rows="3" placeholder="Detalles opcionales..."></textarea>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 text-success">
                                            <i class="bi bi-power"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Estado del Medio</h6>
                                            <small class="text-muted">Habilitar para uso inmediato</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="inputActivo" style="width: 3em; height: 1.5em; cursor: pointer;" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER MODAL --}}
                    <div class="modal-footer border-top-0 p-4 pt-0">
                        <button type="button" class="btn btn-light text-muted fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm hover-lift">
                            <i class="bi bi-check-lg me-1"></i> Guardar Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        let modalObj = null;
        const formMedio = document.getElementById('formMedio');
        const inputNombre = document.getElementById('inputNombre');
        const formMethod = document.getElementById('formMethod');

        document.addEventListener('DOMContentLoaded', function() {
            modalObj = new bootstrap.Modal(document.getElementById('modalMedio'));
            
            // Inicializar Tooltips de Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function nuevoMedio() {
            formMedio.reset();
            formMedio.classList.remove('was-validated'); // Limpiar validación visual
            formMedio.action = "{{ route('correspondencia.medios-recepcion.store') }}";
            formMethod.value = 'POST';
            
            document.getElementById('modalTitle').innerText = 'Nuevo Medio';
            document.getElementById('inputActivo').checked = true;
            
            modalObj.show();
            setTimeout(() => inputNombre.focus(), 500); // Foco automático
        }

        function editMedio(data) {
            formMedio.reset();
            formMedio.classList.remove('was-validated');
            formMedio.action = `/correspondencia/medios-recepcion/${data.id}`;
            formMethod.value = 'PUT';
            
            document.getElementById('modalTitle').innerText = 'Editar Medio';

            // Cargar datos
            inputNombre.value = data.nombre;
            document.getElementById('inputDescripcion').value = data.descripcion || '';
            document.getElementById('inputActivo').checked = Boolean(data.activo);

            modalObj.show();
        }
    </script>
</x-base-layout>