<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">

                    <div class="text-center mb-5">
                        <i class="bi bi-person-fill-gear fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Editar Empleado</h3>
                        <p class="text-muted">Actualiza los datos de <strong class="text-dark">{{ $empleado->nombre_completo }}</strong>.</p>
                    </div>

                    {{-- Incluimos el formulario. La variable $empleado se pasa automáticamente. --}}
                    @include('archivo.empleado.form')

                </div>
            </div>
        </div>
    </div>
</x-base-layout>