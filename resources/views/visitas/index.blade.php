<x-base-layout>
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bolder mb-0 text-gray-800">
                <i class="feather-file-text text-primary me-2"></i> Visitas Corpen
            </h4>
            <div class="fs-6 text-muted">Total de Visitas: {{ $visitas->count() }}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createVisitaModal">
                <i class="feather-plus-circle me-1"></i> Nueva Visita
            </button>
        </div>
    </div>

    <div class="row">
        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-lg-8">
            @forelse($visitas as $visita)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title text-dark fw-bold mb-1">{{ $visita->cliente?->nom_ter ?? 'Visita Rápida' }}</h5>
                            <small class="text-muted">{{ $visita->fecha->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('visitas.corpen.edit', $visita->id) }}" class="btn btn-sm btn-warning">
                                <i class="feather-edit-2"></i>
                            </a>
                            <form action="{{ route('visitas.corpen.destroy', $visita->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta visita?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="feather-trash-2"></i></button>
                            </form>
                        </div>
                    </div>
                    <p class="text-gray-700 bg-light-subtle p-3 rounded border">{{ $visita->motivo ?? 'Sin motivo especificado' }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-2 text-muted fs-7">
                        <span><i class="feather-map-pin me-1"></i> Ciudad: {{ $visita->banco ?? 'No aplica' }}</span>
                        <span><i class="feather-user me-1"></i> Registrado por: {{ $visita->registrado_por }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-light text-center">
                <i class="feather-info me-1"></i> No hay visitas registradas.
            </div>
            @endforelse
        </div>

        {{-- BARRA LATERAL --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title text-dark fw-bold mb-0">
                        <i class="feather-list me-2 text-primary"></i> Información General
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-gray-700 mb-2">Puedes registrar una nueva visita o editar las existentes haciendo click en los botones de cada tarjeta.</p>
                    <hr>
                    <p class="fw-bold text-muted">Total de Visitas Registradas:</p>
                    <h3>{{ $visitas->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREAR VISITA --}}
    <div class="modal fade" id="createVisitaModal" tabindex="-1" aria-labelledby="createVisitaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('visitas.corpen.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createVisitaLabel">Registrar Nueva Visita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body position-relative">
                    <div class="mb-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <input type="text" name="cliente" id="cliente" class="form-control" placeholder="Escribe cédula o nombre">
                        <input type="hidden" name="cedula" id="cedula"> <!-- Se guarda la cédula real -->
                        <div id="cliente-suggestions" class="position-absolute w-100"></div>
                    </div>
                    <div class="mb-3">
                        <label for="banco" class="form-label">Ciudad</label>
                        <input type="text" name="banco" id="banco" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo</label>
                        <textarea name="motivo" id="motivo" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="registrado_por" class="form-label">Registrado Por</label>
                        <input type="text" name="registrado_por" id="registrado_por" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Visita</button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT AUTOCOMPLETADO CLIENTE --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputCliente = document.getElementById('cliente');
        const inputCedula = document.getElementById('cedula');
        const suggestionsDiv = document.getElementById('cliente-suggestions');

        inputCliente.addEventListener('input', function() {
            const query = this.value;
            if(query.length >= 2){
                fetch(`{{ route('visitas.cliente.buscar') }}?query=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        suggestionsDiv.innerHTML = '<ul class="list-group m-0">';
                        data.forEach(c => {
                            const li = document.createElement('li');
                            li.textContent = `${c.cod_ter} - ${c.nom_ter}`;
                            li.dataset.cedula = c.cod_ter;
                            li.classList.add('list-group-item', 'list-group-item-action');
                            li.addEventListener('click', () => {
                                inputCliente.value = li.textContent;
                                inputCedula.value = li.dataset.cedula;
                                suggestionsDiv.innerHTML = '';
                            });
                            suggestionsDiv.querySelector('ul').appendChild(li);
                        });
                    });
            } else {
                suggestionsDiv.innerHTML = '';
            }
        });
    });
    </script>

    <style>
        #cliente-suggestions {
            z-index: 1000;
        }
    </style>
</x-base-layout>
