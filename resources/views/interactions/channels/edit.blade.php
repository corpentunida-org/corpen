<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                
                {{-- Navegación superior --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.channels.index') }}" class="text-muted text-decoration-none">Canales</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        
                        {{-- Cabecera del Formulario --}}
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-warning-soft text-warning rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-edit-3 fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Editar Canal</h3>
                            <p class="text-secondary">Actualizando: <span class="fw-bold text-dark">{{ $channel->name }}</span></p>
                        </div>

                        {{-- Formulario --}}
                        <form action="{{ route('interactions.channels.update', $channel->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('interactions.channels._form', ['buttonText' => 'Actualizar Cambios'])
                        </form>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('interactions.channels.show', $channel->id) }}" class="text-muted small text-decoration-none">
                        <i class="feather-eye me-1"></i> Ver detalles actuales del canal
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>