<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px; border-radius: 20px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-edit text-warning fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0">Editar TRD</h3>
                        <p class="text-muted small m-0">Modificando: <span class="text-dark fw-semibold">{{ $trd->serie_documental }}</span></p>
                    </div>
                </div>
                
                <form action="{{ route('correspondencia.trds.update', $trd) }}" method="POST">
                    @csrf 
                    @method('PUT')
                    
                    {{-- Mantenemos el usuario original --}}
                    <input type="hidden" name="usuario_id" value="{{ $trd->usuario_id }}">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Serie Documental</label>
                        <input type="text" name="serie_documental" class="form-control form-control-lg @error('serie_documental') is-invalid @enderror" value="{{ old('serie_documental', $trd->serie_documental) }}" required>
                        @error('serie_documental')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Flujo de Trabajo (Campo Actualizado) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-route me-1"></i> Flujo de Trabajo Asignado
                        </label>
                        <select name="fk_flujo" class="form-select form-select-lg @error('fk_flujo') is-invalid @enderror" required>
                            @foreach($flujos as $flujo)
                                <option value="{{ $flujo->id }}" {{ old('fk_flujo', $trd->fk_flujo) == $flujo->id ? 'selected' : '' }}>
                                    {{ $flujo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Cambiar el flujo afectará a los nuevos documentos radicados con esta serie.</div>
                        @error('fk_flujo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiempo Archivo Gestión (Años)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-briefcase text-info"></i></span>
                                <input type="number" name="tiempo_gestion" class="form-control" value="{{ old('tiempo_gestion', $trd->tiempo_gestion) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiempo Archivo Central (Años)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-building text-warning"></i></span>
                                <input type="number" name="tiempo_central" class="form-control" value="{{ old('tiempo_central', $trd->tiempo_central) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Disposición Final</label>
                        <select name="disposicion_final" class="form-select form-select-lg">
                            <option value="conservar" {{ old('disposicion_final', $trd->disposicion_final) == 'conservar' ? 'selected' : '' }}>
                                📂 Conservación Total
                            </option>
                            <option value="eliminar" {{ old('disposicion_final', $trd->disposicion_final) == 'eliminar' ? 'selected' : '' }}>
                                🗑️ Eliminación
                            </option>
                        </select>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="fas fa-sync-alt me-2"></i> Actualizar TRD
                        </button>
                        <a href="{{ route('correspondencia.trds.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>