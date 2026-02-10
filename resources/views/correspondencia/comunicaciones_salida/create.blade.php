<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 1000px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4">Generar Comunicación de Salida</h3>
                
                <form action="{{ route('correspondencia.comunicaciones-salida.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="fk_usuario" value="{{ auth()->id() }}">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vincular a Radicado Origen</label>
                            <select name="id_correspondencia" class="form-select @error('id_correspondencia') is-invalid @enderror" required>
                                <option value="">Seleccione radicado...</option>
                                @foreach($correspondencias as $corr)
                                    <option value="{{ $corr->id_radicado }}">Rad: {{ $corr->nro_radicado }} - {{ $corr->asunto }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nro. Oficio Salida</label>
                            <input type="text" name="nro_oficio_salida" class="form-control" placeholder="OF-2024-001" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Plantilla Base</label>
                            <select name="id_plantilla" class="form-select">
                                <option value="">Ninguna (Texto libre)</option>
                                @foreach($plantillas as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->nombre_plantilla }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado del Envío</label>
                            <select name="estado_envio" class="form-select" required>
                                <option value="borrador">Borrador</option>
                                <option value="en_revision">En Revisión</option>
                                <option value="enviado">Enviado / Firmado</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Cuerpo de la Carta / Respuesta</label>
                            <textarea name="cuerpo_carta" class="form-control" rows="10" placeholder="Escriba el contenido oficial aquí..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Subir PDF Firmado (Opcional)</label>
                            <input type="file" name="ruta_pdf" class="form-control" accept="application/pdf">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Generación</label>
                            <input type="date" name="fecha_generacion" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mt-5 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow">Guardar Comunicación</button>
                        <a href="{{ route('correspondencia.comunicaciones-salida.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>