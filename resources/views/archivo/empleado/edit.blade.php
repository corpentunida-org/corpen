<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Editar Empleado: {{ $empleado->nombre1 }} {{ $empleado->apellido1 }}</h5>

            @include('archivo.empleado.form')
        </div>
    </div>
</x-base-layout>
