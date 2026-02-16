<x-base-layout>
    <div class="app-container py-4">
        {{-- HEADER --}}
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0">Medios de Recepción</h1>
                <p class="text-muted small">Configuración de canales de entrada de documentos.</p>
            </div>
            <button class="btn btn-primary shadow-sm" onclick="nuevoMedio()">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Medio
            </button>
        </header>

        {{-- TABLA --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 15%;">Código</th>
                                <th style="width: 25%;">Nombre</th>
                                <th style="width: 35%;">Descripción</th>
                                <th style="width: 10%;">Estado</th>
                                <th class="text-end pe-4" style="width: 15%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medios as $medio)
                            <tr>
                                <td class="ps-4">
                                    <code class="fw-bold text-indigo bg-light px-2 py-1 rounded">{{ $medio->codigo }}</code>
                                </td>
                                <td class="fw-semibold">{{ $medio->nombre }}</td>
                                <td class="text-muted small">{{ Str::limit($medio->descripcion, 60) }}</td>
                                <td>
                                    <span class="badge {{ $medio->activo ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                        {{ $medio->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary border rounded-3" 
                                            onclick="editMedio({{ $medio->toJson() }})"
                                            title="Editar registro">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $medios->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalMedio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="formMedio" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="modal-header bg-dark text-white rounded-top-4">
                        <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Medio de Recepción</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-uppercase">Nombre del Medio</label>
                                <input type="text" name="nombre" id="inputNombre" class="form-control form-control-lg" 
                                       placeholder="Ej: Correo Electrónico" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-uppercase">Código del Sistema</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-code-slash"></i></span>
                                    <input type="text" name="codigo" id="inputCodigo" class="form-control" 
                                           placeholder="CORREO_ELECT" required>
                                </div>
                                <div class="form-text text-primary" id="codigoHelper">Se genera automáticamente, pero puedes editarlo.</div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label small fw-bold text-uppercase">Descripción (Opcional)</label>
                                <textarea name="descripcion" id="inputDescripcion" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                    <label class="form-check-label fw-bold mb-0" for="inputActivo">¿ESTADO ACTIVO?</label>
                                    <input class="form-check-input ms-0" type="checkbox" name="activo" value="1" id="inputActivo" style="width: 2.5em; height: 1.25em;" checked>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let modalObj = null;
        const formMedio = document.getElementById('formMedio');
        const inputNombre = document.getElementById('inputNombre');
        const inputCodigo = document.getElementById('inputCodigo');
        const formMethod = document.getElementById('formMethod');

        document.addEventListener('DOMContentLoaded', function() {
            modalObj = new bootstrap.Modal(document.getElementById('modalMedio'));

            // LÓGICA DE AUTO-GENERACIÓN DE CÓDIGO (SLUG)
            inputNombre.addEventListener('input', function() {
                // Solo autogenerar si estamos CREANDO (para no romper integraciones viejas al editar)
                if (formMethod.value === 'POST') {
                    let slug = inputNombre.value
                        .toUpperCase()
                        .normalize("NFD")
                        .replace(/[\u0300-\u036f]/g, "") // Quita acentos
                        .replace(/[^A-Z0-9]/g, '_')     // Solo letras, números y guiones bajos
                        .replace(/_+/g, '_')             // Evita múltiples guiones bajos
                        .substring(0, 20);               // Límite de largo
                    
                    inputCodigo.value = slug;
                }
            });
        });

        function nuevoMedio() {
            formMedio.reset();
            formMedio.action = "{{ route('correspondencia.medios-recepcion.store') }}";
            formMethod.value = 'POST';
            document.getElementById('modalTitle').innerText = 'Nuevo Medio de Recepción';
            document.getElementById('inputActivo').checked = true;
            document.getElementById('codigoHelper').style.display = 'block';
            modalObj.show();
        }

        function editMedio(data) {
            formMedio.reset();
            formMedio.action = `/correspondencia/medios-recepcion/${data.id}`;
            formMethod.value = 'PUT';
            document.getElementById('modalTitle').innerText = 'Editar: ' + data.nombre;
            document.getElementById('codigoHelper').style.display = 'none'; // Ocultar ayuda en edición

            // Cargar datos
            inputCodigo.value = data.codigo;
            inputNombre.value = data.nombre;
            document.getElementById('inputDescripcion').value = data.descripcion || '';
            document.getElementById('inputActivo').checked = Boolean(data.activo);

            modalObj.show();
        }
    </script>

    <style>
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
        .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
        .text-indigo { color: #6610f2; }
    </style>
</x-base-layout>