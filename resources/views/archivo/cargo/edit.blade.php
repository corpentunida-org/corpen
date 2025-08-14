<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">
                    
                    {{-- Cabecera Minimalista --}}
                    <div class="text-center mb-5">
                        <i class="bi bi-briefcase-fill fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Editar Cargo</h3>
                        <p class="text-muted">Ajusta los detalles del puesto: <strong class="text-dark">{{ $cargo->nombre_cargo }}</strong></p>
                    </div>

                    <form method="POST" action="{{ route('archivo.cargo.update', $cargo->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('archivo.cargo.form')

                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('archivo.cargo.index') }}" class="btn btn-light rounded-pill px-4 py-2">Cancelar</a>
                            <button class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift" type="submit"><i class="bi bi-check-lg me-1"></i> Actualizar Cargo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>