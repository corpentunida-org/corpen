<x-base-layout>
    <div class="card mb-4">
        <div class="card-body">
            {{-- HEADER --}}
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Detalle del Soporte</h5>
                <a href="{{ route('soportes.soportes.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i>
                    <span>Volver al Listado</span>
                </a>
            </div>

            {{-- CAMPOS DEL SOPORTE --}}
            <div class="mb-3">
                <label class="fw-bold">Detalles del Soporte:</label>
                <p class="form-control-plaintext">{{ $soporte->detalles_soporte }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Tipo de Soporte:</label>
                <p class="form-control-plaintext">{{ $soporte->tipo->nombre ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Prioridad:</label>
                <p class="form-control-plaintext">{{ $soporte->prioridad->nombre ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Asignación Inicial :</label>
                <p class="form-control-plaintext">
                    {{ $soporte->maeTercero->cod_ter ?? '' }} - {{ $soporte->maeTercero->nom_ter ?? '' }}
                </p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Creado por:</label>
                <p class="form-control-plaintext">{{ $soporte->usuario->name ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Cargo / Área:</label>
                <p class="form-control-plaintext">{{ $soporte->cargo->nombre_cargo ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Línea de Crédito:</label>
                <p class="form-control-plaintext">{{ $soporte->lineaCredito->nombre ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Fecha de Creación:</label>
                <p class="form-control-plaintext">{{ $soporte->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="mb-3">
                <label class="fw-bold">Última Actualización:</label>
                <p class="form-control-plaintext">{{ $soporte->updated_at->format('d/m/Y H:i') }}</p>
            </div>

            {{-- =========================================
                 SECCIÓN DE OBSERVACIONES
            ========================================== --}}
            <div class="mt-5">
                <h5 class="fw-bold">Observaciones</h5>

                @forelse($soporte->observaciones as $obs)
                    <div class="border rounded p-3 mb-3">
                        <p class="mb-1 fw-semibold">{{ $obs->observacion }}</p>
                        <small class="text-muted d-block">
                            <strong>Usuario:</strong> {{ $obs->usuario->name ?? 'N/A' }} |
                            <strong>Estado:</strong> {{ $obs->estado->nombre ?? 'N/A' }} |
                            <strong>Tipo:</strong> {{ $obs->tipoObservacion->nombre ?? 'N/A' }} |
                            <strong>Fecha:</strong> {{ $obs->timestam ? \Carbon\Carbon::parse($obs->timestam)->format('d/m/Y H:i') : $obs->created_at->format('d/m/Y H:i') }}
                        </small>

                        <form action="{{ route('soportes.soportes.observaciones.destroy', [$soporte->id, $obs->id]) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('¿Eliminar observación?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger ms-2">Eliminar</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No hay observaciones registradas.</p>
                @endforelse

                {{-- FORMULARIO PARA AGREGAR NUEVA OBSERVACIÓN --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <form action="{{ route('soportes.soportes.observaciones.store', $soporte->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="fw-bold">Nueva Observación:</label>
                                <textarea name="observacion" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Estado:</label>
                                <select name="id_scp_estados" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Tipo de Observación:</label>
                                <select id="tipoObservacion" name="id_tipo_observacion" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($tiposObservacion as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SELECT DE USUARIOS PARA ESCALAMIENTO --}}
                            <div class="mb-3 d-none" id="usuarioEscalamiento">
                                <label class="fw-bold">Asignar a Usuario:</label>
                                <select name="id_users" class="form-select">
                                    <option value="">Seleccione...</option>
                                    @foreach(\App\Models\User::select('id','name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Solo requerido si la observación es un escalamiento.</small>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="feather-plus me-1"></i> Agregar Observación
                            </button>
                        </form>
                    </div>
                </div>

                {{-- SCRIPT PARA MOSTRAR EL SELECT SOLO EN ESCALAMIENTO --}}
                @push('scripts')
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const tipoObs = document.getElementById("tipoObservacion");
                        const usuarioEscalamiento = document.getElementById("usuarioEscalamiento");

                        tipoObs.addEventListener("change", function () {
                            const selected = tipoObs.options[tipoObs.selectedIndex].text.toLowerCase();
                            if (selected.includes("escalamiento")) {
                                usuarioEscalamiento.classList.remove("d-none");
                            } else {
                                usuarioEscalamiento.classList.add("d-none");
                            }
                        });
                    });
                </script>
                @endpush

            </div>
            {{-- ========================================= --}}

            {{-- BOTONES DE ACCIÓN --}}
            <div class="mt-4 d-flex justify-content-end">
                <a href="{{ route('soportes.soportes.edit', $soporte->id) }}" class="btn btn-warning me-2">
                    <i class="feather-edit me-1"></i> Editar
                </a>
                <form action="{{ route('soportes.soportes.destroy', $soporte->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este soporte?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-trash-2 me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
