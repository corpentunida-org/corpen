@php
    $esEdicion = isset($correspondencia);
    $fechaValor = old('fecha_solicitud', $esEdicion ? $correspondencia->fecha_solicitud->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
@endphp

<style>
    /* Estilos UX Mejorados */
    .form-section-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        margin-bottom: 20px;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #0d6efd;
        display: inline-block;
        padding-bottom: 5px;
    }
    .input-readonly-custom {
        background-color: #f8f9fa !important;
        border-style: dashed !important; /* Indica visualmente que es inalterable */
        cursor: not-allowed;
        color: #6c757d;
    }
    .select2-container--bootstrap-5 {
        z-index: 1;
    }
</style>

<div class="container-fluid">
    <div class="row g-4">
        
        {{-- SECCIÓN 1: DATOS BÁSICOS --}}
        <div class="col-12">
            <div class="form-section-card">
                <div class="form-section-title">
                    <i class="bi bi-info-circle-fill me-2"></i> Información General 
                    @if($esEdicion && $correspondencia->finalizado)
                        <span class="badge bg-danger ms-2 animate__animated animate__flash">RADICADO FINALIZADO</span>
                    @endif
                </div>

                <div class="row g-3">
                    {{-- ID RADICADO --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">ID Radicado</label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white fw-bold">RAD-</span>
                            <input type="text" name="id_radicado" id="id_radicado"
                                class="form-control fw-bold input-readonly-custom" 
                                value="{{ old('id_radicado', $esEdicion ? $correspondencia->id_radicado : ($siguienteId ?? '')) }}" 
                                readonly tabindex="-1">
                        </div>
                    </div>

                    {{-- FECHA DE CREACIÓN (BLOQUEADA) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary">Fecha de Creación</label>
                        <input type="datetime-local" 
                               class="form-control input-readonly-custom" 
                               value="{{ $fechaValor }}" 
                               readonly 
                               tabindex="-1">
                        {{-- Hidden para asegurar que el dato viaje al controlador --}}
                        <input type="hidden" name="fecha_solicitud" value="{{ $fechaValor }}">
                    </div>

                    {{-- MEDIO DE RECEPCIÓN --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Medio de Recepción <span class="text-danger">*</span></label>
                        <select name="medio_recibido" class="form-select @error('medio_recibido') is-invalid @enderror" required 
                                {{ $esEdicion ? 'disabled' : '' }}>
                            <option value="">Seleccione...</option>
                            @foreach($medios as $medio)
                                <option value="{{ $medio->id }}" {{ old('medio_recibido', $correspondencia->medio_recibido ?? '') == $medio->id ? 'selected' : '' }}>
                                    {{ $medio->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if($esEdicion) <input type="hidden" name="medio_recibido" value="{{ $correspondencia->medio_recibido }}"> @endif
                    </div>

                    {{-- ASUNTO --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">Asunto <span class="text-danger">*</span></label>
                        <input type="text" name="asunto" class="form-control @error('asunto') is-invalid @enderror {{ $esEdicion ? 'input-readonly-custom' : '' }}" 
                               value="{{ old('asunto', $correspondencia->asunto ?? '') }}" placeholder="Resumen breve del documento" required
                               {{ $esEdicion ? 'readonly' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 2: CLASIFICACIÓN Y ASIGNACIÓN --}}
        <div class="col-12">
            <div class="form-section-card">
                <div class="form-section-title text-primary">
                    <i class="bi bi-diagram-3-fill me-2"></i> Clasificación y Responsables
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Remitente (Tercero) <span class="text-danger">*</span></label>
                        <select name="remitente_id" class="form-select select2-remitente @error('remitente_id') is-invalid @enderror" id="remitente_id" required
                                {{ $esEdicion ? 'disabled' : '' }}>
                            <option value="">Escriba código o nombre...</option>
                            @foreach($remitentes as $rem)
                                <option value="{{ $rem->cod_ter }}" {{ old('remitente_id', $correspondencia->remitente_id ?? '') == $rem->cod_ter ? 'selected' : '' }}>
                                    [{{ $rem->cod_ter }}] - {{ $rem->nom_ter }}
                                </option>
                            @endforeach
                        </select>
                        @if($esEdicion) <input type="hidden" name="remitente_id" value="{{ $correspondencia->remitente_id }}"> @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary">Responsable Interno</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person-fill-lock"></i></span>
                            <input type="text" class="form-control bg-light" value="{{ $esEdicion ? $correspondencia->usuario->name : auth()->user()->name }}" readonly tabindex="-1">
                            <input type="hidden" name="usuario_id" value="{{ $esEdicion ? $correspondencia->usuario_id : auth()->id() }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Flujo de Trabajo <span class="text-danger">*</span></label>
                        <select name="flujo_id" id="flujo_id" class="form-select border-primary @error('flujo_id') is-invalid @enderror" required
                                {{ $esEdicion ? 'disabled' : '' }}>
                            <option value="">Seleccione flujo...</option>
                            @foreach($flujos as $f)
                                <option value="{{ $f->id }}" {{ old('flujo_id', $correspondencia->flujo_id ?? '') == $f->id ? 'selected' : '' }}>
                                    {{ $f->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if($esEdicion) <input type="hidden" name="flujo_id" value="{{ $correspondencia->flujo_id }}"> @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">TRD (Retención) <span class="text-danger">*</span></label>
                        <select name="trd_id" id="trd_id" class="form-select @error('trd_id') is-invalid @enderror" required 
                            {{ (!$esEdicion && !old('flujo_id')) || $esEdicion ? 'disabled' : '' }}>
                            @if($esEdicion)
                                <option value="{{ $correspondencia->trd_id }}">{{ $correspondencia->trd->serie_documental ?? 'Seleccionado' }}</option>
                            @else
                                <option value="">-- Primero seleccione flujo --</option>
                            @endif
                        </select>
                        @if($esEdicion) <input type="hidden" name="trd_id" value="{{ $correspondencia->trd_id }}"> @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Estado Actual <span class="text-danger">*</span></label>
                        <select name="estado_id" class="form-select @error('estado_id') is-invalid @enderror" required
                                {{ $esEdicion ? 'disabled' : '' }}>
                            @foreach($estados as $est)
                                <option value="{{ $est->id }}" {{ old('estado_id', $correspondencia->estado_id ?? '') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                            @endforeach
                        </select>
                        @if($esEdicion) <input type="hidden" name="estado_id" value="{{ $correspondencia->estado_id }}"> @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: ADJUNTOS Y OBSERVACIONES --}}
        <div class="col-md-8">
            <div class="form-section-card h-100">
                <div class="form-section-title"><i class="bi bi-chat-text-fill me-2"></i> Observaciones</div>
                <textarea name="observacion_previa" class="form-control {{ $esEdicion ? 'input-readonly-custom' : '' }}" rows="6" 
                          {{ $esEdicion ? 'readonly' : '' }}>{{ old('observacion_previa', $correspondencia->observacion_previa ?? '') }}</textarea>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-section-card h-100">
                <div class="form-section-title"><i class="bi bi-paperclip me-2"></i> Archivo</div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Documento Digital</label>
                    <input type="file" name="documento_arc" class="form-control" {{ $esEdicion ? 'disabled' : '' }}>
                    @if($esEdicion && $correspondencia->documento_arc)
                        <div class="mt-2">
                            <a href="{{ $correspondencia->getFile($correspondencia->documento_arc) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-file-earmark-pdf"></i> Ver Documento
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="bg-light p-3 rounded border">
                    <div class="form-check form-switch">
                        <input type="hidden" name="es_confidencial" value="0">
                        <input class="form-check-input" type="checkbox" name="es_confidencial" value="1" id="confi" 
                               {{ old('es_confidencial', $correspondencia->es_confidencial ?? false) ? 'checked' : '' }}
                               {{ $esEdicion ? 'disabled' : '' }}>
                        <label class="form-check-label fw-bold" for="confi">🔒 Confidencial</label>
                    </div>
                    
                    @if($esEdicion)
                        <div class="form-check form-switch border-top pt-2 mt-2">
                            <input type="hidden" name="finalizado" value="0">
                            <input class="form-check-input" type="checkbox" name="finalizado" value="1" id="fin" 
                                   {{ old('finalizado', $correspondencia->finalizado ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="fin">FINALIZAR RADICADO</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Select2 con tema Bootstrap 5
    if ($.fn.select2) {
        $('.select2-remitente').select2({
            theme: 'bootstrap-5',
            placeholder: "Escriba código o nombre...",
            allowClear: true,
            width: '100%'
        });
    }

    // Lógica AJAX para TRD (Solo en creación)
    @if(!$esEdicion)
    $('#flujo_id').on('change', function() {
        const flujoId = $(this).val();
        const trdSelect = $('#trd_id');
        
        trdSelect.empty().append('<option value="">Cargando...</option>');
        
        if (!flujoId) {
            trdSelect.empty().append('<option value="">-- Primero seleccione flujo --</option>').prop('disabled', true);
            return;
        }

        $.ajax({
            url: `/correspondencia/ajax/trds-por-flujo/${flujoId}`,
            type: 'GET',
            success: function(data) {
                trdSelect.empty().append('<option value="">Seleccione TRD...</option>');
                if (data.length > 0) {
                    $.each(data, function(key, trd) {
                        trdSelect.append(`<option value="${trd.id_trd}">${trd.serie_documental} (${trd.tiempo_gestion} años)</option>`);
                    });
                    trdSelect.prop('disabled', false);
                } else {
                    trdSelect.append('<option value="">Sin series asociadas</option>').prop('disabled', true);
                }
            }
        });
    });
    @endif
});
</script>