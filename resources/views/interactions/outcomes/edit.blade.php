<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.outcomes.index') }}" class="text-muted text-decoration-none">Resultados</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-success-soft text-success rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-edit-3 fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Editar Resultado</h3>
                            <p class="text-secondary">Actualizando: <span class="fw-bold text-dark">{{ $outcome->name }}</span></p>
                        </div>

                        <form action="{{ route('interactions.outcomes.update', $outcome->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('interactions.outcomes._form', ['buttonText' => 'Actualizar Resultado'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7f6; }
        .fw-black { font-weight: 800; }
        .tracking-tight { letter-spacing: -0.02em; }
        .icon-shape { display: flex; align-items: center; justify-content: center; }
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>