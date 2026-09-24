<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.lineas.index') }}" class="text-muted text-decoration-none">Líneas</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-warning-soft text-warning rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-plus-circle fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Crear Línea</h3>
                            <p class="text-secondary opacity-75">Agrega una nueva línea de obligación/producto.</p>
                        </div>

                        <form action="{{ route('interactions.lineas.store') }}" method="POST">
                            @include('interactions.lineas._form')

                            <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                                <a href="{{ route('interactions.lineas.index') }}" class="btn btn-link text-muted text-decoration-none fw-semibold">Cancelar</a>
                                <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm d-flex align-items-center gap-2">
                                    <i class="feather-check"></i> <span>Guardar Línea</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
