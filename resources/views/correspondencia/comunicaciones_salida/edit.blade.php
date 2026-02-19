<x-base-layout>
    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 1000px; border-radius: 20px;">
            <div class="card-body p-5">
                
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box bg-soft-warning text-warning rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fffbeb;">
                        <i class="fas fa-edit fa-lg"></i>
                    </div>
                    <h3 class="fw-bold m-0">Modificar Comunicación: {{ $comunicacionSalida->nro_oficio_salida }}</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 shadow-sm border-0">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('correspondencia.comunicaciones-salida.update', $comunicacionSalida) }}" method="POST" enctype="multipart/form-data">
                    @csrf 
                    @method('PUT')
                    
                    {{-- Mantenemos el usuario original que creó el documento --}}
                    <input type="hidden" name="fk_usuario" value="{{ $comunicacionSalida->fk_usuario }}">

                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Radicado Origen Vinculado</label>
                            <select name="id_correspondencia" class="form-select @error('id_correspondencia') is-invalid @enderror" required>
                                <option value="">Seleccione radicado...</option>
                                @foreach($correspondencias as $corr)
                                    <option value="{{ $corr->id_radicado }}" {{ old('id_correspondencia', $comunicacionSalida->id_correspondencia) == $corr->id_radicado ? 'selected' : '' }}>
                                        Rad: {{ $corr->id_radicado }} - {{ $corr->asunto }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nro. Oficio Salida</label>
                            <input type="text" name="nro_oficio_salida" class="form-control @error('nro_oficio_salida') is-invalid @enderror" value="{{ old('nro_oficio_salida', $comunicacionSalida->nro_oficio_salida) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Plantilla Aplicada</label>
                            <select name="id_plantilla" class="form-select @error('id_plantilla') is-invalid @enderror">
                                <option value="">Ninguna (Manual)</option>
                                @foreach($plantillas as $plan)
                                    <option value="{{ $plan->id }}" {{ old('id_plantilla', $comunicacionSalida->id_plantilla) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->nombre_plantilla }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado del Envío</label>
                            <select name="estado_envio" class="form-select @error('estado_envio') is-invalid @enderror" required>
                                <option value="Generado" {{ old('estado_envio', $comunicacionSalida->estado_envio) == 'Generado' ? 'selected' : '' }}>Generado</option>
                                <option value="Enviado por Email" {{ old('estado_envio', $comunicacionSalida->estado_envio) == 'Enviado por Email' ? 'selected' : '' }}>Enviado por Email</option>
                                <option value="Notificado Físicamente" {{ old('estado_envio', $comunicacionSalida->estado_envio) == 'Notificado Físicamente' ? 'selected' : '' }}>Notificado Físicamente</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Cuerpo del Mensaje</label>
                            <textarea name="cuerpo_carta" class="form-control @error('cuerpo_carta') is-invalid @enderror" rows="12" required>{{ old('cuerpo_carta', $comunicacionSalida->cuerpo_carta) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Actualizar PDF Firmado (Opcional)</label>
                            <input type="file" name="ruta_pdf" class="form-control @error('ruta_pdf') is-invalid @enderror" accept="application/pdf">
                            
                            @if($comunicacionSalida->ruta_pdf)
                                <div class="form-text text-success mt-2">
                                    <i class="fas fa-check-circle me-1"></i> Ya existe un archivo cargado. Suba uno nuevo solo si desea reemplazarlo.
                                </div>
                            @else
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-magic me-1"></i> El sistema genera el PDF automáticamente. Suba un archivo solo si desea reemplazar el automático por uno manual.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Generación</label>
                            <input type="date" name="fecha_generacion" class="form-control @error('fecha_generacion') is-invalid @enderror" value="{{ old('fecha_generacion', $comunicacionSalida->fecha_generacion ? $comunicacionSalida->fecha_generacion->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                    </div>

                    <div class="mt-5 border-top pt-4 d-flex justify-content-between">
                        <a href="{{ route('correspondencia.comunicaciones-salida.index') }}" class="btn btn-light px-4 border">Volver al listado</a>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-5 fw-bold shadow">
                                <i class="fas fa-save me-2"></i> Actualizar Comunicación
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-base-layout>