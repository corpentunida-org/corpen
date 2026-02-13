<x-base-layout>
    <div class="app-container py-4">
        {{-- HEADER --}}
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0">Medios de Recepción</h1>
                <p class="text-muted small">Configuración de canales de entrada de documentos.</p>
            </div>
            <button class="btn btn-primary" onclick="nuevoMedio()">
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
                                <th class="ps-4">Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medios as $medio)
                            <tr>
                                <td class="ps-4 fw-bold text-indigo">{{ $medio->codigo }}</td>
                                <td>{{ $medio->nombre }}</td>
                                <td class="text-muted small">{{ Str::limit($medio->descripcion, 50) }}</td>
                                <td>
                                    <span class="badge {{ $medio->activo ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} rounded-pill px-3">
                                        {{ $medio->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border rounded-3" 
                                            onclick="editMedio({{ $medio->toJson() }})">
                                        <i class="bi bi-pencil-square text-primary"></i>
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
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">CÓDIGO</label>
                                <input type="text" name="codigo" id="inputCodigo" class="form-control" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">NOMBRE</label>
                                <input type="text" name="nombre" id="inputNombre" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">DESCRIPCIÓN</label>
                                <textarea name="descripcion" id="inputDescripcion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                    <label class="form-check-label fw-bold mb-0" for="inputActivo text-uppercase">¿Estado Activo?</label>
                                    <input class="form-check-input ms-0" type="checkbox" name="activo" value="1" id="inputActivo" style="width: 2.5em; height: 1.25em;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let modalObj = null;

        document.addEventListener('DOMContentLoaded', function() {
            modalObj = new bootstrap.Modal(document.getElementById('modalMedio'));
        });

        function nuevoMedio() {
            const form = document.getElementById('formMedio');
            form.reset();
            form.action = "{{ route('correspondencia.medios-recepcion.store') }}";
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('modalTitle').innerText = 'Nuevo Medio de Recepción';
            document.getElementById('inputActivo').checked = true;
            modalObj.show();
        }

        function editMedio(data) {
            const form = document.getElementById('formMedio');
            form.reset();

            // Usamos el ID para construir la URL de actualización
            form.action = `/correspondencia/medios-recepcion/${data.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('modalTitle').innerText = 'Editar Medio: ' + data.nombre;

            // Cargar datos en los inputs
            document.getElementById('inputCodigo').value = data.codigo;
            document.getElementById('inputNombre').value = data.nombre;
            document.getElementById('inputDescripcion').value = data.descripcion || '';
            
            // Sincronizar el switch de estado
            document.getElementById('inputActivo').checked = (data.activo == 1 || data.activo == true);

            modalObj.show();
        }
    </script>
</x-base-layout>