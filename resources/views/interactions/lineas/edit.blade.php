<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.lineas.index') }}" class="text-muted text-decoration-none">Líneas</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-warning-soft text-warning rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-edit-3 fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Editar Línea</h3>
                            <p class="text-secondary">Modificando: <span class="fw-bold text-dark">{{ $linea->name }}</span></p>
                        </div>

                        <form action="{{ route('interactions.lineas.update', $linea->id) }}" method="POST">
                            @method('PUT')
                            @include('interactions.lineas._form')

                            <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                                <a href="{{ route('interactions.lineas.index') }}" class="btn btn-link text-muted text-decoration-none fw-semibold">Cancelar</a>
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm d-flex align-items-center gap-2">
                                    <i class="feather-save"></i> <span>Actualizar Línea</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4 text-muted small italic">
                    <i class="feather-info me-1"></i> El cambio de nombre se reflejará en todas las interacciones asociadas.
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
