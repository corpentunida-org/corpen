<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-soft-warning p-3 rounded-circle me-3" style="background-color: #fff3cd;">
                        <i class="fas fa-edit text-warning fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0">Editar Seguimiento</h3>
                        <p class="text-muted m-0">Radicado: <strong>#{{ $correspondenciaProceso->id_correspondencia }}</strong></p>
                    </div>
                </div>
                
                <form action="{{ route('correspondencia.correspondencias-procesos.update', $correspondenciaProceso) }}" method="POST" enctype="multipart/form-data">
                    @csrf 
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Etapa del Proceso</label>
                            <select name="id_proceso" class="form-select" required>
                                @foreach($procesos_disponibles as $p)
                                    <option value="{{ $p->id }}" {{ $correspondenciaProceso->id_proceso == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }} ({{ $p->flujo->nombre ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado de la Gestión</label>
                            <select name="estado" class="form-select" required>
                                @foreach($estados as $e)
                                    @php $slug = Str::slug($e->nombre, '_'); @endphp
                                    <option value="{{ $slug }}" {{ $correspondenciaProceso->estado == $slug ? 'selected' : '' }}>
                                        {{ $e->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observaciones de la Gestión</label>
                            <textarea name="observacion" class="form-control" rows="5" required>{{ old('observacion', $correspondenciaProceso->observacion) }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Documento de Soporte</label>
                            
                            @if($correspondenciaProceso->documento_arc)
                                <div class="d-flex align-items-center p-2 border rounded-3 bg-light mb-2">
                                    <i class="fas fa-file-alt text-primary mx-3 fs-4"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted d-block">Archivo actual:</small>
                                        <a href="{{ Storage::disk('s3')->url($correspondenciaProceso->documento_arc) }}" target="_blank" class="text-decoration-none fw-bold small">Ver documento cargado</a>
                                    </div>
                                    <span class="badge bg-white text-dark border me-2">Existente</span>
                                </div>
                            @endif

                            <div class="input-group">
                                <input type="file" name="documento_arc" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                                <span class="input-group-text"><i class="fas fa-upload"></i></span>
                            </div>
                            <div class="form-text">Si selecciona un archivo nuevo, el anterior será reemplazado en S3.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha y Hora de Gestión</label>
                            <input type="datetime-local" name="fecha_gestion" class="form-control" 
                                   value="{{ $correspondenciaProceso->fecha_gestion ? $correspondenciaProceso->fecha_gestion->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}" required>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-4 p-3 border rounded-3 w-100 {{ $correspondenciaProceso->notificado_email ? 'bg-soft-success' : '' }}">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="notificado_email" value="1" id="notifCheck" {{ $correspondenciaProceso->notificado_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="notifCheck">
                                    {{ $correspondenciaProceso->notificado_email ? 'Notificación Enviada' : '¿Marcar como Notificado?' }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-5 fw-bold shadow">
                            <i class="fas fa-sync-alt me-2"></i>Actualizar Gestión
                        </button>
                        <a href="{{ route('correspondencia.correspondencias-procesos.show', $correspondenciaProceso) }}" class="btn btn-light border px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>