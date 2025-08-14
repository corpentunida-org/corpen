<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-7">
            
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">

                    {{-- Cabecera adaptada para "Crear" --}}
                    <div class="text-center mb-4">
                        <i class="bi bi-diagram-3 fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Nueva Área</h3>
                        <p class="text-muted">Crea una nueva área funcional para la organización.</p>
                    </div>

                    <form method="POST" action="{{ route('archivo.area.store') }}">
                        @csrf

                        {{-- Se incluye el formulario pasándole una instancia vacía del modelo para evitar errores --}}
                        @include('archivo.area.form', ['area' => new \App\Models\Archivo\GdoArea()])

                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('archivo.area.index') }}" class="btn btn-light rounded-pill px-4 py-2">
                                Cancelar
                            </a>
                            <button class="btn btn-primary rounded-pill px-4 py-2 btn-hover-lift" type="submit">
                                <i class="bi bi-check-lg me-1"></i> Guardar Área
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-base-layout>