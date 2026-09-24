<x-base-layout>
    @section('titlepage', 'Nueva Línea de Crédito')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('lineas_credito.index') }}" class="text-muted text-decoration-none">Líneas de Crédito</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </nav>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-black text-dark mb-1">Nueva Línea de Crédito</h3>
                        <p class="text-secondary opacity-75 mb-4">Define un nuevo producto de crédito.</p>
                        <form action="{{ route('lineas_credito.store') }}" method="POST">
                            @include('creditos.lineas_credito._form', ['buttonText' => 'Crear Línea'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .fw-black { font-weight: 800; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>
