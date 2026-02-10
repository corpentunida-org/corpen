<div class="row g-4">
    {{-- SECCIÓN 1: DATOS BÁSICOS --}}
    <div class="col-12">
        <div class="form-section-title">
            <i class="bi bi-info-circle-fill me-2"></i> Información General
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">ID Radicado <span class="text-danger">*</span></label>
        <div class="input-group has-validation">
            <span class="input-group-text bg-primary text-white fw-bold">RAD-</span>
            <input type="text" name="id_radicado" id="id_radicado"
                class="form-control fw-bold @error('id_radicado') is-invalid @enderror" 
                value="{{ old('id_radicado', isset($correspondencia) ? $correspondencia->id_radicado : ($siguienteId ?? '')) }}" 
                readonly 
                style="background-color: #f8f9fa; cursor: not-allowed;">
            @error('id_radicado') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Fecha de Solicitud <span class="text-danger">*</span></label>
        <input type="datetime-local" name="fecha_solicitud" class="form-control @error('fecha_solicitud') is-invalid @enderror" 
               value="{{ old('fecha_solicitud', isset($correspondencia) ? $correspondencia->fecha_solicitud->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Medio de Recepción <span class="text-danger">*</span></label>
        <select name="medio_recibido" class="form-select @error('medio_recibido') is-invalid @enderror" required>
            <option value="">Seleccione...</option>
            @foreach(['Correo Electrónico', 'Físico / Ventanilla', 'WhatsApp', 'Página Web', 'Otro'] as $medio)
                <option value="{{ $medio }}" {{ old('medio_recibido', $correspondencia->medio_recibido ?? '') == $medio ? 'selected' : '' }}>{{ $medio }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-bold">Asunto <span class="text-danger">*</span></label>
        <input type="text" name="asunto" class="form-control @error('asunto') is-invalid @enderror" 
               value="{{ old('asunto', $correspondencia->asunto ?? '') }}" placeholder="Resumen breve del documento" required>
    </div>

    {{-- SECCIÓN 2: CLASIFICACIÓN Y ASIGNACIÓN --}}
    <div class="col-12 mt-4">
        <div class="form-section-title">
            <i class="bi bi-diagram-3-fill me-2"></i> Clasificación y Responsables
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-primary">Remitente (Tercero) <span class="text-danger">*</span></label>
        <select name="remitente_id" class="form-select select2-remitente @error('remitente_id') is-invalid @enderror" id="remitente_id" required>
            <option value="">Escriba código o nombre del tercero...</option>
            @foreach($remitentes as $rem)
                <option value="{{ $rem->cod_ter }}" {{ old('remitente_id', $correspondencia->remitente_id ?? '') == $rem->cod_ter ? 'selected' : '' }}>
                    [{{ $rem->cod_ter }}] - {{ $rem->nom_ter }}
                </option>
            @endforeach
        </select>
        @error('remitente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Responsable Interno</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-person-fill-lock"></i></span>
            <input type="text" class="form-control bg-light" 
                   value="{{ isset($correspondencia) ? $correspondencia->usuario->name : auth()->user()->name }}" 
                   readonly tabindex="-1">
            <input type="hidden" name="usuario_id" value="{{ isset($correspondencia) ? $correspondencia->usuario_id : auth()->id() }}">
        </div>
        <small class="text-muted italic">Asignado automáticamente al usuario en sesión.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-primary">Flujo de Trabajo <span class="text-danger">*</span></label>
        <select name="flujo_id" id="flujo_id" class="form-select border-primary @error('flujo_id') is-invalid @enderror" required>
            <option value="">Seleccione un flujo...</option>
            @foreach($flujos as $f)
                <option value="{{ $f->id }}" {{ old('flujo_id', $correspondencia->flujo_id ?? '') == $f->id ? 'selected' : '' }}>
                    {{ $f->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">TRD (Retención) <span class="text-danger">*</span></label>
        <div class="d-flex align-items-center gap-2">
            <select name="trd_id" id="trd_id" class="form-select @error('trd_id') is-invalid @enderror" required 
                {{ !isset($correspondencia) && !old('flujo_id') ? 'disabled' : '' }}>
                <option value="">-- Primero seleccione flujo --</option>
                @if(isset($correspondencia) || old('flujo_id'))
                    @foreach($trds as $trd)
                        @if($trd->fk_flujo == old('flujo_id', $correspondencia->flujo_id ?? ''))
                            <option value="{{ $trd->id_trd }}" {{ old('trd_id', $correspondencia->trd_id ?? '') == $trd->id_trd ? 'selected' : '' }}>
                                {{ $trd->serie_documental }} ({{ $trd->tiempo_gestion }} días)
                            </option>
                        @endif
                    @endforeach
                @endif
            </select>
            <div id="trd-loader" class="spinner-border spinner-border-sm text-primary d-none" role="status"></div>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Estado Actual <span class="text-danger">*</span></label>
        <select name="estado_id" class="form-select @error('estado_id') is-invalid @enderror" required>
            @foreach($estados as $est)
                <option value="{{ $est->id }}" {{ old('estado_id', $correspondencia->estado_id ?? '') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
            @endforeach
        </select>
    </div>

    {{-- SECCIÓN 3: ARCHIVOS Y EXTRAS --}}
    <div class="col-12 mt-4">
        <div class="form-section-title">
            <i class="bi bi-paperclip me-2"></i> Adjuntos y Detalles
        </div>
    </div>

    <div class="col-md-8">
        <label class="form-label fw-bold">Observación Previa</label>
        <textarea name="observacion_previa" class="form-control" rows="4">{{ old('observacion_previa', $correspondencia->observacion_previa ?? '') }}</textarea>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label fw-bold">Documento Digital</label>
            <input type="file" name="documento_arc" class="form-control">
            @if(isset($correspondencia) && $correspondencia->documento_arc)
                <div class="mt-2 small text-primary">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <a href="{{ $correspondencia->getFile($correspondencia->documento_arc) }}" target="_blank">Ver documento actual</a>
                </div>
            @endif
        </div>
        
        <div class="bg-light p-3 rounded border">
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="es_confidencial" value="0">
                <input class="form-check-input" type="checkbox" name="es_confidencial" value="1" id="confi" {{ old('es_confidencial', $correspondencia->es_confidencial ?? false) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="confi">¿Confidencial?</label>
            </div>
            <div class="form-check form-switch">
                <input type="hidden" name="finalizado" value="0">
                <input class="form-check-input" type="checkbox" name="finalizado" value="1" id="fin" {{ old('finalizado', $correspondencia->finalizado ?? false) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-primary" for="fin">Finalizado</label>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS DE INTERACCIÓN --}}
<script>
$(document).ready(function() {
    // 1. Select2 para Terceros
    if ($.fn.select2) {
        $('.select2-remitente').select2({
            theme: 'bootstrap-5',
            placeholder: "Escriba código o nombre...",
            allowClear: true,
            width: '100%'
        });
    }

    // 2. Lógica de anidamiento AJAX: Flujo -> TRD
    $('#flujo_id').on('change', function() {
        const flujoId = $(this).val();
        const trdSelect = $('#trd_id');
        const loader = $('#trd-loader');

        // Limpiar select de TRD
        trdSelect.empty().append('<option value="">Cargando series...</option>');
        
        if (!flujoId) {
            trdSelect.empty().append('<option value="">-- Primero seleccione flujo --</option>').prop('disabled', true);
            return;
        }

        loader.removeClass('d-none');

        $.ajax({
            url: `/correspondencia/ajax/trds-por-flujo/${flujoId}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                trdSelect.empty().append('<option value="">Seleccione TRD...</option>');
                
                if (data.length > 0) {
                    $.each(data, function(key, trd) {
                        trdSelect.append(`<option value="${trd.id_trd}">${trd.serie_documental} (${trd.tiempo_gestion} días)</option>`);
                    });
                    trdSelect.prop('disabled', false);
                } else {
                    trdSelect.append('<option value="">Sin series asociadas</option>');
                    trdSelect.prop('disabled', true);
                }
            },
            error: function() {
                alert('Error al conectar con el servidor.');
                trdSelect.empty().append('<option value="">Error de carga</option>');
            },
            complete: function() {
                loader.addClass('d-none');
            }
        });
    });
});
</script>