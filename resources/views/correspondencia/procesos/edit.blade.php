<x-base-layout>
    <div class="app-container">
        <nav class="breadcrumbs mb-4">
            <a href="{{ route('correspondencia.procesos.index') }}" class="crumb-link text-decoration-none">Procesos</a>
            <span class="crumb-separator mx-2">/</span>
            <span class="crumb-current fw-bold">Editar Proceso #{{ $proceso->id }}</span>
        </nav>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 15px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box bg-soft-warning text-warning me-3" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #fffbeb;">
                        <i class="fas fa-edit fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0">Modificar Proceso</h3>
                        <p class="text-muted m-0">Actualice la configuración de la instancia actual.</p>
                    </div>
                </div>

                <hr class="mb-4 opacity-10">

                <form action="{{ route('correspondencia.procesos.update', $proceso) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Enviamos el ID del creador oculto para que la validación del Request no falle --}}
                    <input type="hidden" name="usuario_creador_id" value="{{ $proceso->usuario_creador_id }}">

                    <div class="row g-4">
                        {{-- Usuario Creador (Solo Lectura) --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase">Responsable del Inicio (No editable)</label>
                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr($proceso->creador->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $proceso->creador->name }}</div>
                                    <div class="small text-muted">ID de Usuario: {{ $proceso->usuario_creador_id }}</div>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-lock text-muted" title="Este campo no se puede editar"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Selección de Flujo --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Flujo de Trabajo Asociado</label>
                            <select name="flujo_id" class="form-select select-picker @error('flujo_id') is-invalid @enderror" required>
                                @foreach($flujos as $f)
                                    <option value="{{ $f->id }}" {{ (old('flujo_id', $proceso->flujo_id) == $f->id) ? 'selected' : '' }}>
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('flujo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Detalle o Notas --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Detalle del Proceso</label>
                            <textarea name="detalle" class="form-control @error('detalle') is-invalid @enderror" rows="4">{{ old('detalle', $proceso->detalle) }}</textarea>
                            @error('detalle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                        <i class="fas fa-save me-2"></i> Actualizar Proceso
                                    </button>
                                    <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-light px-4 border">
                                        Cancelar
                                    </a>
                                </div>
                                <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-outline-indigo px-4">
                                    <i class="fas fa-users me-2"></i> Gestionar Equipo
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>