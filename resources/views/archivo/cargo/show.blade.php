<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Detalle del Cargo</h5>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre:</strong> {{ $cargo->nombre_cargo }}</p>
                    <p><strong>Teléfono:</strong> {{ $cargo->telefono_coporativo ?? '-' }}</p>
                    <p><strong>Celular:</strong> {{ $cargo->celular_coporativo ?? '-' }}</p>
                    <p><strong>Extensión:</strong> {{ $cargo->ext_coporativo ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Correo Corporativo:</strong> {{ $cargo->correo_corporativo ?? '-' }}</p>
                    <p><strong>Gmail Corporativo:</strong> {{ $cargo->gmail_coporativo ?? '-' }}</p>
                    <p><strong>Salario Base:</strong> {{ $cargo->salario_base ?? '-' }}</p>
                    <p><strong>Jornada:</strong> {{ $cargo->jornada ?? '-' }}</p>
                </div>
                <div class="col-md-12">
                    <p><strong>Estado:</strong> {{ $cargo->estado ?? '-' }}</p>
                    <p><strong>Observación:</strong> {{ $cargo->observacion ?? '-' }}</p>
                    @if ($cargo->manual_funciones)
                        <p><strong>Manual de Funciones:</strong> 
                            <a href="{{ asset('storage/' . $cargo->manual_funciones) }}" target="_blank">Ver documento</a>
                        </p>
                    @endif
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('archivo.cargo.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</x-base-layout>
