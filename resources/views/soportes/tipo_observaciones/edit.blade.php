<x-base-layout>
    @section('titlepage', 'Editar Tipo de Observación')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-info text-white">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">Editar Tipo de Observación</div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Actualiza el nombre del tipo de observación.
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('soportes.tipoObservaciones.update', $scpTipoObservacion) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                id="nombre" name="nombre" value="{{ old('nombre', $scpTipoObservacion->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-success" type="submit"><i class="feather-save me-2"></i> Guardar Cambios</button>
                        <a href="{{ route('soportes.tablero') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
