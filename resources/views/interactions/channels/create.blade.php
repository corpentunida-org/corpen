<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                
                {{-- Navegación superior --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.channels.index') }}" class="text-muted text-decoration-none">Canales</a></li>
                        <li class="breadcrumb-item active">Crear nuevo</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        
                        {{-- Cabecera del Formulario --}}
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-primary-soft text-primary rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-plus-circle fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Nuevo Canal</h3>
                            <p class="text-secondary opacity-75">Configura un nuevo medio de comunicación para tus interacciones.</p>
                        </div>

                        {{-- Formulario --}}
                        <form action="{{ route('interactions.channels.store') }}" method="POST">
                            @csrf
                            @include('interactions.channels._form', ['buttonText' => 'Guardar Canal'])
                        </form>

                    </div>
                </div>

                {{-- Pie de página del formulario --}}
                <p class="text-center mt-4 text-muted small">
                    <i class="feather-info me-1"></i> Recuerda que los nombres de los canales deben ser únicos.
                </p>
            </div>
        </div>
    </div>
</x-base-layout>