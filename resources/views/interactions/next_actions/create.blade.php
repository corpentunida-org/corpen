<x-base-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                
                {{-- Breadcrumbs --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('interactions.next_actions.index') }}" class="text-muted text-decoration-none">Próximas Acciones</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        
                        {{-- Header Form --}}
                        <div class="text-center mb-5">
                            <div class="icon-shape bg-indigo-soft text-indigo rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
                                <i class="feather-plus-circle fs-2"></i>
                            </div>
                            <h3 class="fw-black tracking-tight text-dark mb-1">Nueva Acción</h3>
                            <p class="text-secondary opacity-75">Define un nuevo paso para la planificación de gestiones.</p>
                        </div>

                        <form action="{{ route('interactions.next_actions.store') }}" method="POST">
                            @csrf
                            @include('interactions.next_actions._form', ['nextAction' => null, 'buttonText' => 'Crear Acción'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>