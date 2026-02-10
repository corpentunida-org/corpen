<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4 text-warning"><i class="fas fa-edit me-2"></i>Editar Seguimiento</h3>
                
                <form action="{{ route('correspondencia.correspondencias-procesos.update', $correspondenciaProceso) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">Estado Actual</label>
                        <select name="estado" class="form-select" required>
                            <option value="recibido" {{ $correspondenciaProceso->estado == 'recibido' ? 'selected' : '' }}>Recibido</option>
                            <option value="en_tramite" {{ $correspondenciaProceso->estado == 'en_tramite' ? 'selected' : '' }}>En Trámite</option>
                            <option value="devuelto" {{ $correspondenciaProceso->estado == 'devuelto' ? 'selected' : '' }}>Devuelto</option>
                            <option value="finalizado" {{ $correspondenciaProceso->estado == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Observación</label>
                        <textarea name="observacion" class="form-control" rows="5" required>{{ old('observacion', $correspondenciaProceso->observacion) }}</textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Gestión</label>
                            <input type="datetime-local" name="fecha_gestion" class="form-control" value="{{ $correspondenciaProceso->fecha_gestion->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="notificado_email" value="1" {{ $correspondenciaProceso->notificado_email ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">¿Notificado por Email?</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow">Actualizar Seguimiento</button>
                    <a href="{{ route('correspondencia.correspondencias-procesos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>