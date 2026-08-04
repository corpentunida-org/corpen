@if(isset($inmueble) && $inmueble)
    {{-- SI SE SOLICITÓ VER EL DETALLE, SE MUESTRA ESTE BLOQUE --}}
    @include('rsv.admin.partials.inmuebles.show')
@else
    {{-- 1. LA TARJETA Y LA TABLA DE INMUEBLES --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 pb-2">
            <div>
                <h5 class="fw-bold mb-1">Gestión de Catálogo</h5>
                <p class="text-muted small mb-0">Administra el inventario de propiedades (rsv_catalogo_inmueble).</p>
            </div>
            {{-- Botón que abre el modal de Bootstrap --}}
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNuevoInmueble">
                + Nuevo Inmueble
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Propiedad</th>
                            <th>Precio Fijo</th>
                            <th>Capacidad</th>
                            <th>Ciudad</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($inmuebles)
                            @forelse($inmuebles as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td class="fw-bold text-dark">{{ $item->name }}</td>
                                    <td>${{ number_format($item->precio_base_noche, 2) }}</td>
                                    <td>{{ $item->capacidad_maxima ?? 'N/A' }} pers.</td>
                                    <td>{{ $item->city ?? 'N/A' }}</td>
                                    <td>{{ $item->ubicacion ?? 'N/A' }}</td>
                                    <td>
                                        @if($item->active)
                                            <span class="badge bg-success bg-opacity-15 text-success px-2 py-1">Activo</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-15 text-secondary px-2 py-1">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            {{-- BOTÓN VER DETALLE --}}
                                            <a href="{{ route('rsv.inmuebles.show', $item->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                Ver
                                            </a>
                                            {{-- BOTÓN EDITAR --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-editar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditarInmueble"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-precio="{{ $item->precio_base_noche }}"
                                                data-capacidad="{{ $item->capacidad_maxima }}"
                                                data-ciudad="{{ $item->city }}"
                                                data-ubicacion="{{ $item->ubicacion }}"
                                                data-active="{{ $item->active }}"
                                                data-tipo="{{ $item->tipo_inmueble_id }}">
                                                Editar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No hay inmuebles registrados todavía.</td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No hay inmuebles registrados todavía.</td>
                            </tr>
                        @endisset
                    </tbody>
                </table>
            </div>

            {{-- Paginación fluida --}}
            @include('rsv.components.pagination', ['paginator' => $inmuebles ?? null])
        </div>
    </div>

    {{-- 2. EL MODAL DE BOOTSTRAP CON TODOS LOS CAMPOS DEL MODELO --}}
    <div class="modal fade @if($errors->any()) show d-block @endif" id="modalNuevoInmueble" tabindex="-1" aria-labelledby="modalNuevoInmuebleLabel" aria-hidden="{{ $errors->any() ? 'false' : 'true' }}" @if($errors->any()) style="background: rgba(0,0,0,0.5);" @endif>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 p-3">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalNuevoInmuebleLabel">Registrar Propiedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @if($errors->any()) onclick="this.closest('.modal').classList.remove('show', 'd-block');" @endif></button>
                </div>

                <div class="modal-body">
                    {{-- Validamos si hay errores generales para mostrarlos arriba --}}
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 small">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('rsv.inmuebles.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Nombre del Inmueble</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Ej. Cabaña de Montaña" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Precio Base por Noche ($)</label>
                                <input type="number" step="0.01" class="form-control @error('precio_base_noche') is-invalid @enderror" name="precio_base_noche" value="{{ old('precio_base_noche') }}" placeholder="0.00" required>
                                @error('precio_base_noche') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Capacidad Máxima (Personas)</label>
                                <input type="number" class="form-control @error('capacidad_maxima') is-invalid @enderror" name="capacidad_maxima" value="{{ old('capacidad_maxima') }}" placeholder="Ej. 4">
                                @error('capacidad_maxima') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Ciudad</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" placeholder="Ej. Bogotá">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Ubicación / Dirección</label>
                                <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej. Calle 100 # 15-20">
                                @error('ubicacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Tipo de Inmueble</label>
                            <select class="form-select @error('tipo_inmueble_id') is-invalid @enderror" name="tipo_inmueble_id" required>
                                <option value="" disabled {{ old('tipo_inmueble_id') ? '' : 'selected' }}>Selecciona un tipo...</option>
                                <option value="1" {{ old('tipo_inmueble_id') == '1' ? 'selected' : '' }}>Apartamento</option>
                                <option value="2" {{ old('tipo_inmueble_id') == '2' ? 'selected' : '' }}>Casa</option>
                            </select>
                            @error('tipo_inmueble_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="active" value="1" id="activeCheck" {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark fw-bold" for="activeCheck">Activo (visible en catálogo)</label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" @if($errors->any()) onclick="this.closest('.modal').classList.remove('show', 'd-block');" @endif>Cancelar</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Inmueble</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. EL MODAL DE BOOTSTRAP PARA EDITAR --}}
    <div class="modal fade" id="modalEditarInmueble" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 p-3">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditarLabel">Editar Propiedad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarInmueble" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Nombre del Inmueble</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Precio Base por Noche ($)</label>
                                <input type="number" step="0.01" class="form-control" id="edit_precio" name="precio_base_noche" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Capacidad Máxima</label>
                                <input type="number" class="form-control" id="edit_capacidad" name="capacidad_maxima">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Ciudad</label>
                                <input type="text" class="form-control" id="edit_ciudad" name="city">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark fw-bold">Ubicación / Dirección</label>
                                <input type="text" class="form-control" id="edit_ubicacion" name="ubicacion">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">Tipo de Inmueble</label>
                            <select class="form-select" id="edit_tipo_inmueble" name="tipo_inmueble_id" required>
                                <option value="1">Apartamento</option>
                                <option value="2">Casa</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                            <label class="form-check-label text-dark fw-bold" for="edit_active">Activo (visible en catálogo)</label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar Inmueble</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const botonesEditar = document.querySelectorAll('.btn-editar');

            botonesEditar.forEach(boton => {
                boton.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const precio = this.getAttribute('data-precio');
                    const capacidad = this.getAttribute('data-capacidad');
                    const ciudad = this.getAttribute('data-ciudad');
                    const ubicacion = this.getAttribute('data-ubicacion');
                    const active = this.getAttribute('data-active');
                    const tipo = this.getAttribute('data-tipo');

                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_precio').value = precio;
                    document.getElementById('edit_capacidad').value = capacidad;
                    document.getElementById('edit_ciudad').value = ciudad;
                    document.getElementById('edit_ubicacion').value = ubicacion;
                    document.getElementById('edit_tipo_inmueble').value = tipo;
                    document.getElementById('edit_active').checked = (active == "1");

                    const urlBase = "{{ route('rsv.inmuebles.update', 0) }}";
                    document.getElementById('formEditarInmueble').action = urlBase.replace('/0', '/' + id);
                });
            });
        });
    </script>
@endif
