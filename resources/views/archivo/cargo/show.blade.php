<x-base-layout>
    <div class="row">
        {{-- Tarjeta principal del Cargo --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" 
                     style="background: linear-gradient(90deg, #264653, #e9c46a); border-left: 5px solid #f4a261;">
                    <i class="bi bi-briefcase-fill me-2"></i> Cargo: {{ $cargo->nombre_cargo }}
                </div>
                <div class="card-body" style="background-color: #fff;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-2"><i class="bi bi-cash-coin me-2 text-success"></i>
                                <strong>Salario Base:</strong><br> 
                                {{ $cargo->salario_base !== null ? '$'.number_format($cargo->salario_base, 2, ',', '.') : '-' }}
                            </p>
                            <p class="mb-2"><i class="bi bi-clock-history me-2 text-primary"></i>
                                <strong>Jornada:</strong><br> {{ $cargo->jornada ?? '-' }}
                            </p>
                            <p class="mb-2"><i class="bi bi-check-circle-fill me-2 text-success"></i>
                                <strong>Estado:</strong><br> 
                                @if($cargo->estado)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><i class="bi bi-telephone-fill me-2 text-warning"></i>
                                <strong>Teléfono Corporativo:</strong><br> {{ $cargo->telefono_corporativo ?? '-' }}
                            </p>
                            <p class="mb-2"><i class="bi bi-phone-fill me-2 text-success"></i>
                                <strong>Celular Corporativo:</strong><br> {{ $cargo->celular_corporativo ?? '-' }}
                            </p>
                            <p class="mb-2"><i class="bi bi-telephone-plus-fill me-2 text-info"></i>
                                <strong>Extensión:</strong><br> {{ $cargo->ext_corporativo ?? '-' }}
                            </p>
                            <p class="mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i>
                                <strong>Correo Corporativo:</strong><br> {{ $cargo->correo_corporativo ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p><i class="bi bi-building me-2 text-primary"></i>
                            <strong>Área:</strong> {{ $cargo->gdoArea->nombre ?? 'Sin área asignada' }}
                        </p>
                        <p><i class="bi bi-journal-text me-2 text-secondary"></i>
                            <strong>Observación:</strong><br> {{ $cargo->observacion ?? 'Sin observaciones.' }}
                        </p>
                        <p><i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i>
                            <strong>Manual de Funciones:</strong><br>
                            @if ($cargo->manual_funciones)
                                <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" target="_blank" 
                                   class="btn btn-sm mt-1" 
                                   style="background: linear-gradient(90deg, #e9c46a, #f4a261); color: #fff; border-radius: 8px;">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> Ver Documento
                                </a>
                            @else
                                <span class="text-muted">No se ha cargado ningún documento.</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta secundaria del Empleado relacionado --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" 
                     style="background: linear-gradient(90deg, #2a9d8f, #264653); border-left: 5px solid #f4a261;">
                    <i class="bi bi-person-badge-fill me-2"></i> Empleado Asignado
                </div>
                <div class="card-body" style="background-color: #fdfcfb;">
                    @if($cargo->empleado)
                        <div class="d-flex align-items-center mb-2">
                            <div style="width: 50px; height: 50px; background-color: #2a9d8f; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">
                                {{ substr($cargo->empleado->nombre_completo, 0, 1) }}
                            </div>
                            <div class="ms-3">
                                <p class="mb-1"><strong>{{ $cargo->empleado->nombre_completo }}</strong></p>
                                <small class="text-muted">Cédula: {{ $cargo->empleado->cedula }}</small>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No hay empleado asignado a este cargo.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Botones de acción --}}
    <div class="text-end mt-4">
        <a href="{{ route('archivo.cargo.edit', $cargo->id) }}" 
           class="btn px-4 py-2 me-2" 
           style="background: linear-gradient(90deg, #e76f51, #f4a261); color: #fff; border-radius: 8px; transition: 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <i class="bi bi-pencil-square me-2"></i> Editar
        </a>
        <a href="{{ route('archivo.cargo.index') }}" 
           class="btn px-4 py-2" 
           style="background: linear-gradient(90deg, #2a9d8f, #264653); color: #fff; border-radius: 8px; transition: 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <i class="bi bi-arrow-left me-2"></i> Volver
        </a>
    </div>

    {{-- Hover para botones --}}
    <style>
        a.btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</x-base-layout>
