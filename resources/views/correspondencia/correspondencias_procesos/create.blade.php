<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-soft-primary p-3 rounded-circle me-3" style="background-color: #e7f1ff;">
                        <i class="fas fa-history text-primary fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0">Registrar Seguimiento</h3>
                        <p class="text-muted mb-0">Gestión de trazabilidad para correspondencia</p>
                    </div>
                </div>
                
                <form action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Seleccionar Radicado <span class="text-danger">*</span></label>
                            <select name="id_correspondencia" class="form-select select2-enable @error('id_correspondencia') is-invalid @enderror" required>
                                <option value="">--- Busque por ID o Asunto ---</option>
                                @foreach($correspondencias as $c)
                                    <option value="{{ $c->id_radicado }}" {{ old('id_correspondencia') == $c->id_radicado ? 'selected' : '' }}>
                                        #{{ $c->id_radicado }} | {{ Str::limit($c->asunto, 75) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_correspondencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Etapa del Proceso</label>
                            <select name="id_proceso" class="form-select @error('id_proceso') is-invalid @enderror" required>
                                <option value="">--- Seleccione Etapa ---</option>
                                @foreach($procesos_disponibles as $p)
                                    <option value="{{ $p->id }}" {{ old('id_proceso') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }} ({{ $p->flujo->nombre ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_proceso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nuevo Estado del Radicado</label>
                            <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                                @foreach($estados as $e)
                                    @php $valEstado = Str::slug($e->nombre, '_'); @endphp
                                    <option value="{{ $valEstado }}" {{ old('estado') == $valEstado ? 'selected' : '' }}>
                                        {{ $e->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Actualizará el estado principal del documento.</div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observaciones de la Gestión</label>
                            <textarea name="observacion" class="form-control @error('observacion') is-invalid @enderror" rows="4" 
                                      placeholder="Describa la acción realizada..." required>{{ old('observacion') }}</textarea>
                            @error('observacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Adjuntar Soporte (Opcional)</label>
                            <div class="input-group">
                                <input type="file" name="documento_arc" class="form-control @error('documento_arc') is-invalid @enderror" accept=".pdf,.doc,.docx,.jpg,.png">
                                <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                            </div>
                            <div class="form-text">PDF, Word o Imágenes (Máx. 10MB).</div>
                            @error('documento_arc')
                                <small class="text-danger d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Gestión</label>
                            <input type="datetime-local" name="fecha_gestion" class="form-control" 
                                   value="{{ old('fecha_gestion', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4 p-3 border rounded-3 w-100 bg-light">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="notificado_email" value="1" id="notifCheck" {{ old('notificado_email') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="notifCheck">
                                    ¿Notificar por correo?
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-3">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i>Guardar Gestión
                        </button>
                        <a href="{{ route('correspondencia.correspondencias-procesos.index') }}" class="btn btn-outline-secondary px-4">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>