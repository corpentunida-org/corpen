<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 700px; border-radius: 20px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-table text-primary fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0">Crear Serie Documental</h3>
                        <p class="text-muted small m-0">Configure la retención y el flujo de trabajo.</p>
                    </div>
                </div>
                
                <form action="{{ route('correspondencia.trds.store') }}" method="POST">
                    @csrf
                    {{-- Usuario Responsable (Automático) --}}
                    <input type="hidden" name="usuario_id" value="{{ auth()->id() }}">

                    {{-- Nombre de la Serie --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre de la Serie Documental</label>
                        <input type="text" name="serie_documental" 
                               class="form-control form-control-lg @error('serie_documental') is-invalid @enderror" 
                               placeholder="Ej: Historias Laborales" 
                               value="{{ old('serie_documental') }}" required>
                        @error('serie_documental')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Flujo de Trabajo (NUEVO CAMPO) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            <i class="fas fa-route me-1"></i> Flujo de Trabajo Asociado
                        </label>
                        <select name="fk_flujo" class="form-select form-select-lg @error('fk_flujo') is-invalid @enderror" required>
                            <option value="" selected disabled>Seleccione un flujo operativo...</option>
                            @foreach($flujos as $flujo)
                                <option value="{{ $flujo->id }}" {{ old('fk_flujo') == $flujo->id ? 'selected' : '' }}>
                                    {{ $flujo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Define el recorrido automático de los documentos de esta serie.</div>
                        @error('fk_flujo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tiempos de Retención --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiempo Archivo Gestión (Años)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-briefcase text-info"></i></span>
                                <input type="number" name="tiempo_gestion" class="form-control @error('tiempo_gestion') is-invalid @enderror" 
                                       value="{{ old('tiempo_gestion', 0) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tiempo Archivo Central (Años)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-building text-warning"></i></span>
                                <input type="number" name="tiempo_central" class="form-control @error('tiempo_central') is-invalid @enderror" 
                                       value="{{ old('tiempo_central', 0) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    {{-- Disposición Final --}}
                    <div class="mb-4 p-3 rounded-3 bg-light border">
                        <label class="form-label fw-bold d-block mb-3">Disposición Final</label>
                        <div class="d-flex gap-5">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="disposicion_final" id="disp1" value="conservar" {{ old('disposicion_final', 'conservar') == 'conservar' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-success" for="disp1">
                                    <i class="fas fa-archive me-1"></i> Conservación Total
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="disposicion_final" id="disp2" value="eliminar" {{ old('disposicion_final') == 'eliminar' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-danger" for="disp2">
                                    <i class="fas fa-trash-alt me-1"></i> Eliminación
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Guardar TRD
                        </button>
                        <a href="{{ route('correspondencia.trds.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>