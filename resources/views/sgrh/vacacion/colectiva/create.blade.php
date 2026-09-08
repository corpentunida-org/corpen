<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Nuevo decreto de vacaciones colectivas</h2>
            <p class="text-muted mb-0">Obligatoria genera automáticamente una solicitud aprobada para cada colaborador del alcance elegido. Bloqueo solo impide solicitar en esas fechas.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.vacacion.colectiva.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('sgrh.vacacion.colectiva.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Tipo</label>
                        <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                            <option value="obligatoria" @selected(old('tipo') === 'obligatoria')>Obligatoria</option>
                            <option value="bloqueo" @selected(old('tipo') === 'bloqueo')>Bloqueo</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Fecha de inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase">Fecha de fin</label>
                        <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin') }}" required>
                        @error('fecha_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small text-uppercase">Descripción / motivo</label>
                        <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" value="{{ old('descripcion') }}" maxlength="500" required>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <hr class="my-1">
                        <p class="text-muted small fw-bold text-uppercase mb-2">Alcance</p>
                        <div class="d-flex gap-4 mb-3">
                            <div class="form-check">
                                <input type="radio" name="alcance" value="empresa" id="alcance_empresa" class="form-check-input" onchange="actualizarAlcance()" {{ old('alcance', 'empresa') === 'empresa' ? 'checked' : '' }}>
                                <label class="form-check-label" for="alcance_empresa">Toda la empresa</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="alcance" value="area" id="alcance_area" class="form-check-input" onchange="actualizarAlcance()" {{ old('alcance') === 'area' ? 'checked' : '' }}>
                                <label class="form-check-label" for="alcance_area">Áreas específicas</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="alcance" value="empleados" id="alcance_empleados" class="form-check-input" onchange="actualizarAlcance()" {{ old('alcance') === 'empleados' ? 'checked' : '' }}>
                                <label class="form-check-label" for="alcance_empleados">Colaboradores específicos</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-none" id="wrapper_areas">
                        <label class="form-label fw-bold text-dark small text-uppercase">Áreas</label>
                        <select name="areas[]" class="form-select" multiple size="6">
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}" @selected(in_array($area->id, old('areas', [])))>{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                        @error('areas')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-none" id="wrapper_empleados">
                        <label class="form-label fw-bold text-dark small text-uppercase">Colaboradores</label>
                        <select name="empleados[]" class="form-select" multiple size="8">
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->id }}" @selected(in_array($empleado->id, old('empleados', [])))>{{ $empleado->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('empleados')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" onclick="return confirm('¿Confirmas este decreto? Si es obligatoria, se generarán solicitudes ya aprobadas para todo el alcance elegido.');">
                        <i class="bi bi-check-circle"></i> Decretar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function actualizarAlcance() {
                const alcance = document.querySelector('input[name="alcance"]:checked')?.value;
                document.getElementById('wrapper_areas').classList.toggle('d-none', alcance !== 'area');
                document.getElementById('wrapper_empleados').classList.toggle('d-none', alcance !== 'empleados');
            }
            document.addEventListener('DOMContentLoaded', actualizarAlcance);

            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif
        </script>
    @endpush
</x-base-layout>
