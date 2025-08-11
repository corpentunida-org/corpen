<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Detalles del Empleado</h5>

            <p><strong>Cédula:</strong> {{ $empleado->cedula }}</p>
            <p><strong>Nombre Completo:</strong> {{ $empleado->apellido1 }} {{ $empleado->apellido2 }} {{ $empleado->nombre1 }} {{ $empleado->nombre2 }}</p>
            <p><strong>Fecha de Nacimiento:</strong> {{ $empleado->nacimiento ? $empleado->nacimiento->format('d-m-Y') : 'No especificada' }}</p>
            <p><strong>Lugar:</strong> {{ $empleado->lugar ?? 'No especificado' }}</p>
            <p><strong>Sexo:</strong> {{ $empleado->sexo == 'M' ? 'Masculino' : ($empleado->sexo == 'F' ? 'Femenino' : 'No especificado') }}</p>
            <p><strong>Correo Personal:</strong> {{ $empleado->correo_personal ?? 'No especificado' }}</p>
            <p><strong>Celular Personal:</strong> {{ $empleado->celular_personal ?? 'No especificado' }}</p>
            <p><strong>Celular Acudiente:</strong> {{ $empleado->celular_acudiente ?? 'No especificado' }}</p>

            <a href="{{ route('archivo.empleado.index') }}" class="btn btn-secondary mt-3">Volver al listado</a>
        </div>
    </div>
</x-base-layout>
