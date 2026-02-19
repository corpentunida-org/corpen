<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 1000px; border-radius: 20px;">
            <div class="card-body p-5">
                <h3 class="fw-bold mb-4">Generar Comunicación de Salida</h3>
                
                @if ($errors->any())
                    <div class="alert alert-danger mb-4 shadow-sm border-0">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('correspondencia.comunicaciones-salida.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="fk_usuario" value="{{ auth()->id() }}">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Vincular a Radicado Origen</label>
                            <select name="id_correspondencia" class="form-select @error('id_correspondencia') is-invalid @enderror" required>
                                <option value="">Seleccione radicado...</option>
                                @foreach($correspondencias as $corr)
                                    <option value="{{ $corr->id_radicado }}" {{ old('id_correspondencia') == $corr->id_radicado ? 'selected' : '' }}>
                                        Rad: {{ $corr->nro_radicado ?? $corr->id_radicado }} - {{ $corr->asunto }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nro. Oficio Salida</label>
                            <input type="text" name="nro_oficio_salida" class="form-control @error('nro_oficio_salida') is-invalid @enderror" placeholder="OF-2024-001" value="{{ old('nro_oficio_salida') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Plantilla Base</label>
                            <select name="id_plantilla" class="form-select">
                                <option value="">Ninguna (Texto libre)</option>
                                @foreach($plantillas as $plan)
                                    <option value="{{ $plan->id }}" {{ old('id_plantilla') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->nombre_plantilla }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado del Envío</label>
                            <select name="estado_envio" class="form-select @error('estado_envio') is-invalid @enderror" required>
                                <option value="Generado" {{ old('estado_envio') == 'Generado' ? 'selected' : '' }}>Generado</option>
                                <option value="Enviado por Email" {{ old('estado_envio') == 'Enviado por Email' ? 'selected' : '' }}>Enviado por Email</option>
                                <option value="Notificado Físicamente" {{ old('estado_envio') == 'Notificado Físicamente' ? 'selected' : '' }}>Notificado Físicamente</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Cuerpo de la Carta / Respuesta</label>
                            <textarea name="cuerpo_carta" class="form-control @error('cuerpo_carta') is-invalid @enderror" rows="10" placeholder="Escriba el contenido oficial aquí..." required>{{ old('cuerpo_carta') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Subir PDF Firmado (Opcional)</label>
                            <input type="file" name="ruta_pdf" class="form-control @error('ruta_pdf') is-invalid @enderror" accept="application/pdf">
                            <small class="text-muted">Si no sube un archivo, el sistema generará uno automáticamente.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Generación</label>
                            <input type="date" name="fecha_generacion" class="form-control @error('fecha_generacion') is-invalid @enderror" value="{{ old('fecha_generacion', date('Y-m-d')) }}">
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