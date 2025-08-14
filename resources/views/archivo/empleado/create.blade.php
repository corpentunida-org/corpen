<x-base-layout>
    <div class="row justify-content-center animate-on-load">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                <div class="card-body p-4 p-lg-5">

                    <div class="text-center mb-5">
                        <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                        <h3 class="card-title fw-light mt-2 mb-0">Nuevo Empleado</h3>
                        <p class="text-muted">Rellena los detalles para registrar a una nueva persona.</p>
                    </div>

                    {{-- 
                        Incluimos el formulario pasándole un modelo vacío.
                        Esto evita errores de "variable $empleado no definida".
                    --}}
                    @include('archivo.empleado.form', ['empleado' => new \App\Models\Archivo\GdoEmpleado()])

                </div>
            </div>
        </div>
    </div>
</x-base-layout>