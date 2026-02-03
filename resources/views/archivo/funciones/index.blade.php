<x-base-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            :root { --card-border: #f0f0f2; --text-main: #2d3748; }

            /* --- FIX CRÍTICO SELECT2 (OPCIÓN 2) --- */
            /* Forzamos que el menú desplegable ignore límites de paneles y se vea sobre el header */
            .select2-container--open .select2-dropdown {
                z-index: 9999999 !important;
            }

            .select2-container {
                z-index: 9999 !important;
            }

            .select2-container .select2-selection--single { 
                height: 40px !important; 
                border-radius: 8px !important; 
                border: 1px solid #e2e8f0 !important;
                display: flex !important;
                align-items: center !important;
            }

            /* --- PANEL COLAPSABLE DINÁMICO (VINCULAR A CARGO) --- */
            #panel-vincular {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                max-height: 1000px;
                overflow: visible !important; /* Permitir que Select2 "salga" del panel */
            }

            /* Estado colapsado (activado por botón o scroll) */
            #panel-vincular.collapsed-mode {
                max-height: 58px !important;
                opacity: 0.9;
                overflow: hidden !important;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }

            /* Expansión rápida al pasar el mouse si está colapsado */
            #panel-vincular.collapsed-mode:hover {
                max-height: 1000px !important;
                opacity: 1;
                overflow: visible !important;
            }

            /* Animación del icono del botón de toggle */
            .btn-toggle-scroll i {
                transition: transform 0.3s ease;
                display: inline-block;
            }
            .collapsed-mode .btn-toggle-scroll i {
                transform: rotate(180deg);
            }

            /* --- ESTILOS GENERALES --- */
            .card-modern { border: 1px solid var(--card-border); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); background: white; overflow: hidden; }
            .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s; border: 1px solid #e2e8f0; background: white; }
            .btn-action:hover { background: #f8fafc; transform: translateY(-1px); }
            
            .animate-fade-up { animation: fadeUp 0.4s ease-out forwards; }
            @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            
            .item-inactivo { background-color: #f8f9fa; opacity: 0.7; }
            .texto-inactivo { text-decoration: line-through; color: #6c757d; }

            #toast-container { z-index: 999999 !important; }
        </style>
    @endpush

    {{-- ENCABEZADO --}}
    <div class="row align-items-center mb-4 animate-fade-up">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Gestión de Funciones</h3>
            <p class="text-muted small mb-0">Administre el catálogo de responsabilidades y sus vinculaciones.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button type="button" class="btn btn-primary px-4 fw-600 shadow-sm border-0 d-inline-flex align-items-center" 
                    style="border-radius: 8px;" onclick="mostrarPanel('crear')">
                <i class="bi bi-plus-lg me-2"></i>Nueva Función
            </button>
        </div>
    </div>

    <div class="row animate-fade-up" style="animation-delay: 0.1s;">
        
        {{-- COLUMNA IZQUIERDA: PANELES --}}
        <div class="col-lg-4">
            
            {{-- 1. PANEL VINCULAR (CON BOTÓN DE CONTROL Y AUTO-COLLAPSE) --}}
            <div id="panel-vincular" class="card card-modern mb-4 panel-seccion">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center" 
                     style="cursor: pointer;" onclick="togglePanelManual()">
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-link me-2"></i>Vincular a Cargo</h6>
                    <button type="button" class="btn btn-sm btn-light btn-toggle-scroll rounded-circle border shadow-sm">
                        <i class="bi bi-chevron-up"></i>
                    </button>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('archivo.funcion.asignarCargo') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Cargo Destino</label>
                            <select name="gdo_cargo_id" class="form-select select2-init" required>
                                <option value="">Seleccione un cargo...</option>
                                @foreach($cargos as $cargo)
                                    <option value="{{ $cargo->id }}">{{ $cargo->nombre_cargo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">Función a Asignar</label>
                            <select name="gdo_funcion_id" class="form-select select2-init" required>
                                <option value="">Seleccione una función...</option>
                                @foreach($funciones as $f)
                                    <option value="{{ $f->id }}">{{ $f->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted d-block">Estado Inicial</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="estado" id="estadoOn" value="1" checked>
                                <label class="btn btn-outline-success" for="estadoOn">Activo</label>
                              
                                <input type="radio" class="btn-check" name="estado" id="estadoOff" value="0">
                                <label class="btn btn-outline-secondary" for="estadoOff">Inactivo</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 shadow-sm py-2" style="border-radius: 8px;">
                            <i class="bi bi-check2-circle me-2"></i>Vincular
                        </button>
                    </form>
                </div>
            </div>

            {{-- 2. PANEL CREAR --}}
            <div id="panel-crear" class="card card-modern mb-4 panel-seccion d-none border-primary">
                <div class="card-header bg-primary text-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Nueva Función</h6>
                    <button type="button" class="btn-close btn-close-white" onclick="mostrarPanel('vincular')"></button>
                </div>
                <div class="card-body">
                    <form action="{{ route('archivo.funcion.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Ej: Supervisar personal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light w-50" onclick="mostrarPanel('vincular')">Cancelar</button>
                            <button type="submit" class="btn btn-primary w-50">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 3. PANEL EDITAR --}}
            <div id="panel-editar" class="card card-modern mb-4 panel-seccion d-none border-warning">
                <div class="card-header bg-warning text-dark py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Editar Función</h6>
                    <button type="button" class="btn-close" onclick="mostrarPanel('vincular')"></button>
                </div>
                <div class="card-body">
                    <form id="form-editar" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light w-50" onclick="mostrarPanel('vincular')">Cancelar</button>
                            <button type="submit" class="btn btn-warning w-50">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: TABLA --}}
        <div class="col-lg-8">
            <div class="card card-modern">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Funciones Registradas</h6>
                    <span class="badge bg-light text-dark border">{{ count($funciones) }} total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Nombre</th>
                                <th>Descripción</th>
                                <th class="text-center">Uso</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($funciones as $f)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $f->nombre }}</td>
                                    <td><span class="text-muted small">{{ Str::limit($f->descripcion, 40) ?: '---' }}</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-primary border">{{ $f->cargos_count }} cargos</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-action" onclick="cargarEditar({{ json_encode($f) }})" title="Editar">
                                                <i class="bi bi-pencil text-primary"></i>
                                            </button>

                                            @if($f->cargos_count == 0)
                                                <form action="{{ route('archivo.funcion.destroy', $f->id) }}" method="POST" class="swal-confirm-form">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-action" title="Eliminar"><i class="bi bi-trash text-danger"></i></button>
                                                </form>
                                            @else
                                                <button class="btn btn-action" style="cursor: not-allowed; opacity: 0.5;" 
                                                        onclick="toastr.warning('No se puede eliminar: Función asignada.')">
                                                    <i class="bi bi-trash text-muted"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No hay registros</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ACORDEÓN INFERIOR --}}
    <div class="row mt-4 animate-fade-up">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-header bg-white py-3 border-0"><h6 class="fw-bold mb-0">Estado de Funciones por Cargo</h6></div>
                <div class="card-body">
                    <div class="accordion accordion-flush" id="accFunciones">
                        @foreach($cargos as $cargo)
                            @if($cargo->funciones->count() > 0)
                            <div class="accordion-item border rounded mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-white text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#c-{{ $cargo->id }}">
                                        <div class="d-flex align-items-center w-100">
                                            <div class="bg-light p-2 rounded me-3"><i class="bi bi-briefcase"></i></div>
                                            <div>
                                                <div class="fw-bold">{{ $cargo->nombre_cargo }}</div>
                                                <div class="text-muted small">{{ $cargo->funciones->count() }} vinculadas</div>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="c-{{ $cargo->id }}" class="accordion-collapse collapse" data-bs-parent="#accFunciones">
                                    <div class="accordion-body bg-light-subtle">
                                        <div class="row g-2">
                                            @foreach($cargo->funciones as $func)
                                            <div class="col-md-4">
                                                <div class="p-2 border rounded d-flex justify-content-between align-items-center {{ $func->pivot->estado ? 'bg-white' : 'item-inactivo' }}">
                                                    <div class="d-flex align-items-center overflow-hidden me-2">
                                                        <i class="bi bi-circle-fill me-2 {{ $func->pivot->estado ? 'text-success' : 'text-secondary' }}" style="font-size: 8px;"></i>
                                                        <span class="small text-truncate {{ $func->pivot->estado ? 'text-dark fw-bold' : 'texto-inactivo' }}" title="{{ $func->nombre }}">
                                                            {{ $func->nombre }}
                                                        </span>
                                                    </div>
                                                    <form action="{{ route('archivo.funcion.cambiarEstadoVinculo', [$cargo->id, $func->id]) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-light border py-0 px-2 text-muted">
                                                            <i class="bi {{ $func->pivot->estado ? 'bi-toggle-on text-success' : 'bi-toggle-off text-secondary' }} fs-4"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // FUNCIÓN PARA EL BOTÓN DE TOGGLE MANUAL
            function togglePanelManual() {
                const panel = document.getElementById('panel-vincular');
                panel.classList.toggle('collapsed-mode');
            }

            document.addEventListener('DOMContentLoaded', function () {
                toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000" };
                
                // INICIALIZACIÓN SELECT2
                $('.select2-init').select2({ 
                    width: '100%',
                    dropdownParent: $(document.body) 
                });

                // --- DETECCIÓN DE SCROLL ROBUSTA ---
                const panelVincular = document.getElementById('panel-vincular');
                
                const handleScroll = () => {
                    // Verificamos scroll de ventana y de elementos del DOM (por si el layout es fijo)
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
                    if (scrollTop > 100) {
                        panelVincular.classList.add('collapsed-mode');
                    } else {
                        panelVincular.classList.remove('collapsed-mode');
                    }
                };

                // Escuchamos el scroll en modo captura (true) para que detecte eventos en cualquier div interno
                window.addEventListener('scroll', handleScroll, true);

                @if (session('success')) toastr.success("{{ session('success') }}"); @endif
                @if (session('error')) toastr.error("{{ session('error') }}"); @endif
                @if (session('warning'))
                    Swal.fire({ icon: 'warning', title: '¡Atención!', text: "{{ session('warning') }}", confirmButtonColor: '#f59e0b', confirmButtonText: 'Entendido' });
                @endif

                $('.swal-confirm-form').on('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Confirmar eliminación?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
                    }).then((result) => { if (result.isConfirmed) { this.submit(); } });
                });
            });

            function mostrarPanel(panelNombre) {
                document.querySelectorAll('.panel-seccion').forEach(el => el.classList.add('d-none'));
                const target = document.getElementById('panel-' + panelNombre);
                if(target) {
                    target.classList.remove('d-none');
                    target.classList.add('animate-fade-up');
                    target.classList.remove('collapsed-mode');
                }
            }

            function cargarEditar(data) {
                const form = document.getElementById('form-editar');
                form.action = `/archivo/funcion/${data.id}`;
                document.getElementById('edit_nombre').value = data.nombre;
                document.getElementById('edit_descripcion').value = data.descripcion;
                mostrarPanel('editar');
            }
        </script>
    @endpush
</x-base-layout>