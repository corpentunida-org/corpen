<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">

                    {{-- Cabecera adaptada para "Crear" --}}
                    <div class="text-center mb-5">
                        <i class="bi bi-briefcase fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Nuevo Cargo</h3>
                        <p class="text-muted">Rellena los detalles para crear un nuevo puesto.</p>
                    </div>

                    <form action="{{ route('archivo.cargo.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Incluimos el formulario pasándole un modelo vacío para evitar errores --}}
                        @include('archivo.cargo.form', ['cargo' => new \App\Models\Archivo\GdoCargo()])

                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('archivo.cargo.index') }}" class="btn btn-light rounded-pill px-4 py-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift"><i class="bi bi-check-lg me-1"></i> Guardar Cargo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>