<x-base-layout>

    {{-- Contenedor principal --}}
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            {{-- Tarjeta --}}
            <div class="card card-friendly animate-on-load">

                {{-- Encabezado de la Tarjeta --}}
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-eye-fill fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold mb-0">Detalles de la Categoría</h4>
                        <small class="text-muted">Información registrada.</small>
                    </div>
                </div>

                {{-- Cuerpo de la Tarjeta --}}
                <div class="card-body p-3 p-lg-4">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Categoría:</label>
                        <p class="form-control-plaintext">{{ $categoria->nombre }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Creación:</label>
                        <p class="form-control-plaintext">{{ $categoria->created_at->format('d/m/Y H:i A') }}</p>
                    </div>

                    <hr class="my-4">

                    {{-- Botones de Acción --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('archivo.categorias.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Volver al Listado
                        </a>
                        <a href="{{ route('archivo.categorias.edit', $categoria) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-fill me-1"></i>
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>