<x-base-layout>
    <div class="app-container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('correspondencia.procesos.index') }}" class="text-decoration-none">Procesos</a></li>
                <li class="breadcrumb-item active fw-bold text-dark">Editar Proceso #{{ $proceso->id }}</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box me-3" style="width: 55px; height: 55px; border-radius: 15px; display: flex; align-items: center; justify-content: center; background: #fffbeb; color: #f59e0b;">
                        <i class="fas fa-edit fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0 h4">Modificar Proceso</h3>
                        <p class="text-muted m-0">Actualice la información principal de la instancia.</p>
                    </div>
                </div>

                <form action="{{ route('correspondencia.procesos.update', $proceso) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        {{-- Nombre del Proceso --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Título / Nombre del Proceso</label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre', $proceso->nombre) }}" required style="border-radius: 10px;">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Selección de Flujo --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Flujo de Trabajo</label>
                            <select name="flujo_id" class="form-select @error('flujo_id') is-invalid @enderror" style="border-radius: 10px;">
                                @foreach($flujos as $f)
                                    <option value="{{ $f->id }}" {{ (old('flujo_id', $proceso->flujo_id) == $f->id) ? 'selected' : '' }}>
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('flujo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Detalle --}}
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-dark">Detalle o Notas</label>
                            <textarea name="detalle" class="form-control" rows="4" style="border-radius: 12px; resize: none;">{{ old('detalle', $proceso->detalle) }}</textarea>
                        </div>

                        <div class="col-md-12 pt-4 border-top">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-light px-4 border" style="border-radius: 10px;">Cancelar</a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="btn btn-outline-primary px-4" style="border-radius: 10px;">
                                        <i class="fas fa-users me-2"></i> Equipo
                                    </a>
                                    <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius: 10px; background: #4f46e5;">
                                        <i class="fas fa-save me-2"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>