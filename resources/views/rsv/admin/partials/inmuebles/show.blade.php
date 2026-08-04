<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-4 pb-2">
        <div>
            <h5 class="fw-bold mb-1">Detalle del Inmueble: {{ $inmueble->name }}</h5>
            <p class="text-muted small mb-0">Información completa de la propiedad ID #{{ $inmueble->id }}</p>
        </div>
        <a href="{{ route('rsv.admin.dashboard') }}" class="btn btn-secondary btn-sm rounded-pill px-3">
            ← Volver al Listado
        </a>
    </div>
    <div class="card-body">

        {{-- Alertas de éxito o error generales --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Nombre de la Propiedad</p>
                <h6 class="fw-bold text-dark">{{ $inmueble->name }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Precio Base por Noche</p>
                <h6 class="fw-bold text-success">${{ number_format($inmueble->precio_base_noche, 2) }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Capacidad Máxima</p>
                <h6 class="fw-bold">{{ $inmueble->capacidad_maxima ?? 'N/A' }} personas</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Ciudad</p>
                <h6 class="fw-bold">{{ $inmueble->city ?? 'N/A' }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Ubicación / Dirección</p>
                <h6 class="fw-bold">{{ $inmueble->ubicacion ?? 'N/A' }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Tipo de Inmueble (ID)</p>
                <h6 class="fw-bold">{{ $inmueble->tipo_inmueble_id }}</h6>
            </div>
            <div class="col-md-6">
                <p class="mb-1 text-muted small">Estado Actual</p>
                <h6>
                    @if($inmueble->active)
                        <span class="badge bg-success bg-opacity-15 text-success px-2 py-1">Activo</span>
                    @else
                        <span class="badge bg-secondary bg-opacity-15 text-secondary px-2 py-1">Inactivo</span>
                    @endif
                </h6>
            </div>
        </div>

        <hr class="my-4">

        {{-- SECCIÓN 1: MULTIMEDIA ASOCIADA --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">Multimedia del Inmueble</h6>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddMultimedia">
                + Agregar Multimedia
            </button>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>URL / Archivo</th>
                        <th>Orden</th>
                        <th>Portada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inmueble->multimedia ?? [] as $media)
                        <tr>
                            <td>{{ $media->id }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $media->tipo_multimedia }}</span></td>
                            <td class="text-truncate" style="max-width: 250px;"><a href="{{ $media->url_archivo }}" target="_blank">{{ $media->url_archivo }}</a></td>
                            <td>{{ $media->orden }}</td>
                            <td>
                                @if($media->es_portada)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted small py-3">No hay elementos multimedia registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- SECCIÓN 2: TARIFAS POR TEMPORADA ASOCIADAS --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">Tarifas por Temporada</h6>
            <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAddTarifa">
                + Nueva Tarifa Temporada
            </button>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Temporada</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Precio Noche</th>
                        <th>Precio Fin de Semana</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inmueble->tarifasTemporadas ?? [] as $tarifa)
                        <tr>
                            <td class="fw-bold">{{ $tarifa->nombre_temporada }}</td>
                            <td>{{ optional($tarifa->fecha_inicio)->format('Y-m-d') }}</td>
                            <td>{{ optional($tarifa->fecha_fin)->format('Y-m-d') }}</td>
                            <td>${{ number_format($tarifa->precio_noche, 2) }}</td>
                            <td>${{ number_format($tarifa->precio_fin_semana, 2) }}</td>
                            <td>
                                @if($tarifa->active)
                                    <span class="badge bg-success bg-opacity-15 text-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-15 text-secondary">Inactiva</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted small py-3">No hay tarifas por temporada registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('rsv.admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Volver al Listado</a>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL 1: REGISTRAR MULTIMEDIA              --}}
{{-- ========================================== --}}
<div class="modal fade" id="modalAddMultimedia" tabindex="-1" aria-labelledby="modalAddMultimediaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalAddMultimediaLabel">Agregar Multimedia a {{ $inmueble->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('rsv.inmueble-multimedia.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">URL del Archivo</label>
                        <input type="text" class="form-control" name="url_archivo" value="{{ old('url_archivo') }}" placeholder="https://..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Tipo de Multimedia</label>
                        <select class="form-select" name="tipo_multimedia" required>
                            <option value="imagen" {{ old('tipo_multimedia') == 'imagen' ? 'selected' : '' }}>Imagen</option>
                            <option value="video" {{ old('tipo_multimedia') == 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Orden</label>
                            <input type="number" class="form-control" name="orden" value="{{ old('orden', 0) }}">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-center pt-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="es_portada" value="1" id="esPortadaCheck" {{ old('es_portada') ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-bold" for="esPortadaCheck">¿Es Portada?</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Multimedia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL 2: REGISTRAR TARIFA TEMPORADA        --}}
{{-- ========================================== --}}
<div class="modal fade" id="modalAddTarifa" tabindex="-1" aria-labelledby="modalAddTarifaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalAddTarifaLabel">Nueva Tarifa por Temporada para {{ $inmueble->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('rsv.tarifas-temporadas.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inmueble->id }}">

                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Nombre de la Temporada</label>
                        <input type="text" class="form-control" name="nombre_temporada" value="{{ old('nombre_temporada') }}" placeholder="Ej. Temporada Alta Diciembre" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Fecha de Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Fecha de Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" value="{{ old('fecha_fin') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Precio por Noche ($)</label>
                            <input type="number" step="0.01" class="form-control" name="precio_noche" value="{{ old('precio_noche') }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Precio Fin de Semana ($)</label>
                            <input type="number" step="0.01" class="form-control" name="precio_fin_semana" value="{{ old('precio_fin_semana') }}" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="active" value="1" id="tarifaActiveCheck" {{ old('active', true) ? 'checked' : '' }}>
                        <label class="form-check-label text-dark fw-bold" for="tarifaActiveCheck">Activa</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4">Guardar Tarifa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT AUXILIAR PARA REABRIR MODALES EN CASO DE ERROR DE VALIDACIÓN --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->has('nombre_temporada') || $errors->has('precio_noche') || $errors->has('precio_fin_semana'))
            var modalTarifa = new bootstrap.Modal(document.getElementById('modalAddTarifa'));
            modalTarifa.show();
        @endif

        @if($errors->has('url_archivo') || $errors->has('tipo_multimedia'))
            var modalMedia = new bootstrap.Modal(document.getElementById('modalAddMultimedia'));
            modalMedia.show();
        @endif
    });
</script>
