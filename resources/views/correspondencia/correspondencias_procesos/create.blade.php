<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 800px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4">Registrar Seguimiento de Correspondencia</h3>
                
                <form action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="fk_usuario" value="{{ auth()->id() }}">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Seleccionar Radicado</label>
                            <select name="id_correspondencia" class="form-select select2" required>
                                <option value="">--- Seleccione Radicado ---</option>
                                {{-- Aquí deberías pasar una lista de correspondencias desde el controlador --}}
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Etapa del Proceso</label>
                            <select name="id_proceso" class="form-select" required>
                                <option value="">--- Seleccione Etapa ---</option>
                                {{-- Aquí deberías pasar los procesos desde el controlador --}}
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nuevo Estado</label>
                            <select name="estado" class="form-select" required>
                                <option value="recibido">Recibido</option>
                                <option value="en_tramite">En Trámite</option>
                                <option value="devuelto">Devuelto</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observaciones de la Gestión</label>
                            <textarea name="observacion" class="form-control" rows="4" placeholder="Describa brevemente la acción realizada..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Gestión</label>
                            <input type="datetime-local" name="fecha_gestion" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="notificado_email" value="1" id="notifCheck">
                                <label class="form-check-label fw-bold" for="notifCheck">¿Notificar por Email?</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow">Guardar Seguimiento</button>
                        <a href="{{ route('correspondencia.correspondencias-procesos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>