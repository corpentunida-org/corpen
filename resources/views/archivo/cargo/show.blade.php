<x-base-layout>
    <div class="card">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Detalle del Cargo</h5>

            <div class="row g-3">
                {{-- Columna Izquierda --}}
                <div class="col-md-6">
                    <p class="mb-2"><strong>Nombre del Cargo:</strong><br> {{ $cargo->nombre_cargo }}</p>
                    <p class="mb-2"><strong>Salario Base:</strong><br> ${{ number_format($cargo->salario_base, 2, ',', '.') ?? '-' }}</p>
                    <p class="mb-2"><strong>Jornada:</strong><br> {{ $cargo->jornada ?? '-' }}</p>
                    <p class="mb-2"><strong>Estado:</strong><br> 
                        @if($cargo->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </p>
                </div>

                {{-- Columna Derecha --}}
                <div class="col-md-6">
                    <p class="mb-2"><strong>Teléfono Corporativo:</strong><br> {{ $cargo->telefono_corporativo ?? '-' }}</p>
                    <p class="mb-2"><strong>Celular Corporativo:</strong><br> {{ $cargo->celular_corporativo ?? '-' }}</p>
                    <p class="mb-2"><strong>Extensión:</strong><br> {{ $cargo->ext_corporativo ?? '-' }}</p>
                    <p class="mb-2"><strong>Correo Corporativo:</strong><br> {{ $cargo->correo_corporativo ?? '-' }}</p>
                </div>

                {{-- Sección de Observaciones y Manual --}}
                <div class="col-md-12 mt-4">
                    <p><strong>Observación:</strong><br> {{ $cargo->observacion ?? 'Sin observaciones.' }}</p>
                    
                    {{-- ESTA ES LA PARTE IMPORTANTE --}}
                    @if ($cargo->manual_funciones)
                        <div class="mt-3">
                            <strong>Manual de Funciones:</strong>
                            <p>
                                {{-- Usamos Storage::url() que es la forma más robusta --}}
                                <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" target="_blank" class="btn btn-outline-danger btn-sm mt-1">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> Ver Documento
                                </a>
                            </p>
                        </div>
                    @else
                        <div class="mt-3">
                           <p><strong>Manual de Funciones:</strong><br> No se ha cargado ningún documento.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('archivo.cargo.edit', $cargo->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-2"></i> Editar
                </a>
                <a href="{{ route('archivo.cargo.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>
</x-base-layout>