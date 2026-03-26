<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.outcomes.index') }}" class="text-muted text-decoration-none">Resultados</a></li>
                        <li class="breadcrumb-item active">Nuevo</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-success-soft text-success rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-plus-circle fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Crear Resultado</h3>
                            <p class="text-secondary opacity-75">Define un nuevo estado para tus gestiones comerciales.</p>
                        </div>

                        <form action="{{ route('interactions.outcomes.store') }}" method="POST">
                            @csrf
                            @include('interactions.outcomes._form', ['buttonText' => 'Guardar Resultado'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>