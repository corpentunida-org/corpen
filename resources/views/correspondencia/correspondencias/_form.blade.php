@php
    $esEdicion = isset($correspondencia);
    // Formateo de fecha para visualización e input
    $fechaVal = old('fecha_solicitud', $esEdicion ? $correspondencia->fecha_solicitud->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
@endphp

<style>
    /* --- UX: ESTILOS DE FICHA TÉCNICA (MODO LECTURA / EDICIÓN) --- */
    .field-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #8898aa;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
        display: block;
    }
    
    .static-value {
        font-size: 0.95rem;
        color: #32325d;
        font-weight: 500;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 5px;
        margin-bottom: 15px;
        min-height: 24px;
        background-color: transparent;
    }

    /* --- UX: ZONA DE ACCIÓN (SOLO APARECE EN MODO EDICIÓN) --- */
    .resolution-card {
        background: linear-gradient(145deg, #ffffff, #f4f7f6);
        border-left: 5px solid #5e72e4; /* Azul corporativo */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .resolution-card.active-closure {
        border-left-color: #2dce89; /* Verde éxito */
        background: #f6fff9;
    }

    .form-switch-lg .form-check-input {
        height: 1.5rem;
        width: 3rem;
        cursor: pointer;
    }
    
    .icon-box {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #525f7f;
        margin-right: 15px;
    }

    /* Ajuste para inputs en modo Create */
    .form-control:focus, .form-select:focus {
        border-color: #5e72e4;
        box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
    }
</style>

<div class="container-fluid py-2">
    
    {{-- ENCABEZADO: INFORMACIÓN GENERAL --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                <div class="icon-box">
                    <i class="bi bi-folder2-open fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-dark">
                        @if($esEdicion) 
                            Radicado <span class="fw-bold">#{{ $correspondencia->id_radicado }}</span> 
                        @else 
                            Nuevo Radicado 
                        @endif
                    </h5>
                    <small class="text-muted">Información base del documento</small>
                </div>
            </div>

            <div class="row g-4">
                {{-- ID RADICADO --}}
                <div class="col-md-3">
                    <label class="field-label">ID Radicado</label>
                    @if($esEdicion)
                        <div class="static-value">{{ $correspondencia->id_radicado }}</div>
                        {{-- Hidden para enviar el dato aunque sea solo lectura --}}
                        <input type="hidden" name="id_radicado" value="{{ $correspondencia->id_radicado }}">
                    @else
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">RAD-</span>
                            <input type="text" name="id_radicado" class="form-control fw-bold border-start-0 ps-0" 
                                   value="{{ old('id_radicado', $siguienteId) }}" readonly>
                        </div>
                    @endif
                </div>

                {{-- FECHA --}}
                <div class="col-md-3">
                    <label class="field-label">Fecha Solicitud</label>
                    @if($esEdicion)
                        <div class="static-value">
                            <i class="bi bi-calendar-event me-1 text-muted"></i> 
                            {{ $correspondencia->fecha_solicitud->format('d/m/Y h:i A') }}
                        </div>
                        <input type="hidden" name="fecha_solicitud" value="{{ $fechaVal }}">
                    @else
                        <input type="datetime-local" class="form-control" value="{{ $fechaVal }}" readonly>
                        <input type="hidden" name="fecha_solicitud" value="{{ $fechaVal }}">
                    @endif
                </div>

                {{-- MEDIO DE RECEPCIÓN --}}
                <div class="col-md-3">
                    <label class="field-label">Medio de Recepción</label>
                    @if($esEdicion)
                        <div class="static-value">{{ $correspondencia->medioRecepcion->nombre ?? 'N/A' }}</div>
                        <input type="hidden" name="medio_recibido" value="{{ $correspondencia->medio_recibido }}">
                    @else
                        <select name="medio_recibido" class="form-select" required>
                            <option value="">Seleccione...</option>
                            @foreach($medios as $m)
                                <option value="{{ $m->id }}" {{ old('medio_recibido') == $m->id ? 'selected' : '' }}>{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                
                {{-- ESTADO --}}
                <div class="col-md-3">
                    <label class="field-label">Estado Inicial</label>
                    @if($esEdicion)
                        <div class="static-value">
                            <span class="badge bg-secondary text-dark border">{{ $correspondencia->estado->nombre ?? 'Desconocido' }}</span>
                        </div>
                        <input type="hidden" name="estado_id" value="{{ $correspondencia->estado_id }}">
                    @else
                        <select name="estado_id" class="form-select" required>
                            @foreach($estados as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- COLUMNA IZQUIERDA: CONTENIDO DEL RADICADO --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="field-label text-primary mb-3 border-bottom pb-2"><i class="bi bi-text-paragraph"></i> Contenido</h6>
                    
                    {{-- ASUNTO --}}
                    <div class="mb-4">
                        <label class="field-label">Asunto <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="static-value fw-bold">{{ $correspondencia->asunto }}</div>
                            <input type="hidden" name="asunto" value="{{ $correspondencia->asunto }}">
                        @else
                            <input type="text" name="asunto" class="form-control" value="{{ old('asunto') }}" 
                                   placeholder="Escriba un resumen breve y claro del documento..." required>
                        @endif
                    </div>

                    {{-- OBSERVACIÓN INICIAL --}}
                    <div class="mb-2">
                        <label class="field-label">Observación Inicial</label>
                        @if($esEdicion)
                            <div class="p-3 bg-light rounded text-muted small fst-italic border">
                                {{ $correspondencia->observacion_previa ?? 'Sin observaciones registradas.' }}
                            </div>
                            <input type="hidden" name="observacion_previa" value="{{ $correspondencia->observacion_previa }}">
                        @else
                            <textarea name="observacion_previa" class="form-control" rows="3" placeholder="Comentarios adicionales al recibir el documento...">{{ old('observacion_previa') }}</textarea>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ZONA DE EDICIÓN EXCLUSIVA: RESOLUCIÓN Y CIERRE (SOLO APARECE EN EDIT) --}}
            @if($esEdicion)
            <div class="card resolution-card mb-4" id="card_cierre">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold text-dark"><i class="bi bi-pen-fill me-2"></i>Resolución de cierre emitida por usuario responsable del cierre</h5>
                            <p class="text-muted small mb-0">Área exclusiva para documentar la respuesta o cierre del trámite.</p>
                        </div>
                        
                        {{-- SWITCH DE FINALIZADO --}}
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" name="finalizado" id="finalizado" value="1" 
                                   {{ old('finalizado', $correspondencia->finalizado) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-2 mt-1" for="finalizado" id="lbl_finalizado">En Proceso</label>
                        </div>
                    </div>

                    <div class="mt-4 animate__animated animate__fadeIn">
                        <label class="field-label text-dark">Descripción de la Gestión / Resolución Final</label>
                        <textarea name="final_descripcion" id="final_descripcion" class="form-control shadow-none" rows="5" 
                                  placeholder="Escriba aquí los detalles de la gestión realizada o la resolución final del caso..."
                                  style="background-color: #fff; border: 1px solid #ced4da;">{{ old('final_descripcion', $correspondencia->final_descripcion) }}</textarea>
                        <div class="form-text mt-2" id="help_desc">
                            <i class="bi bi-info-circle"></i> Esta información será visible en el historial del radicado al finalizar.
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- COLUMNA DERECHA: CLASIFICACIÓN TÉCNICA --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="field-label text-primary mb-3 border-bottom pb-2"><i class="bi bi-sliders"></i> Clasificación</h6>

                    {{-- REMITENTE --}}
                    <div class="mb-4">
                        <label class="field-label">Remitente <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="d-flex align-items-center mt-1">
                                <div class="avatar bg-light text-primary rounded-circle me-2 d-flex justify-content-center align-items-center" style="width:32px; height:32px">
                                    <i class="bi bi-person"></i>
                                </div>
                                <span class="fw-bold text-dark small text-truncate">{{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</span>
                            </div>
                            <input type="hidden" name="remitente_id" value="{{ $correspondencia->remitente_id }}">
                        @else
                            <select name="remitente_id" class="form-select select2-remitente" required>
                                <option value="">Buscar tercero...</option>
                                @foreach($remitentes as $r)
                                    <option value="{{ $r->cod_ter }}" {{ old('remitente_id') == $r->cod_ter ? 'selected' : '' }}>
                                        {{ $r->nom_ter }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- FLUJO --}}
                    <div class="mb-4">
                        <label class="field-label">Flujo de Trabajo <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="static-value">{{ $correspondencia->flujo->nombre ?? 'N/A' }}</div>
                            <input type="hidden" name="flujo_id" value="{{ $correspondencia->flujo_id }}">
                        @else
                            <select name="flujo_id" id="flujo_id" class="form-select" required>
                                <option value="">Seleccione flujo...</option>
                                @foreach($flujos as $f)
                                    <option value="{{ $f->id }}" {{ old('flujo_id') == $f->id ? 'selected' : '' }}>
                                        {{ $f->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- TRD (Aquí estaba el problema, ahora corregido con AJAX) --}}
                    <div class="mb-4">
                        <label class="field-label">Serie TRD <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="static-value">{{ $correspondencia->trd->serie_documental ?? 'N/A' }}</div>
                            <input type="hidden" name="trd_id" value="{{ $correspondencia->trd_id }}">
                        @else
                            {{-- Se inicia deshabilitado hasta que el JS cargue los datos --}}
                            <select name="trd_id" id="trd_id" class="form-select" required disabled>
                                <option value="">-- Seleccione flujo primero --</option>
                            </select>
                        @endif
                    </div>

                    {{-- CONFIDENCIALIDAD --}}
                    <div class="mb-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="field-label mb-0">Confidencialidad</label>
                            @if($esEdicion)
                                <span class="badge {{ $correspondencia->es_confidencial ? 'bg-warning text-dark' : 'bg-light text-muted border' }}">
                                    {{ $correspondencia->es_confidencial ? 'CONFIDENCIAL' : 'PÚBLICO' }}
                                </span>
                                <input type="hidden" name="es_confidencial" value="{{ $correspondencia->es_confidencial ? '1' : '0' }}">
                            @else
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="es_confidencial" value="1" id="es_confidencial">
                                    <label class="form-check-label" for="es_confidencial"></label>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ARCHIVO --}}
                    <div class="mt-4">
                        <label class="field-label">Documento Digital</label>
                        @if($esEdicion && $correspondencia->documento_arc)
                            <div class="card bg-light border p-2 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-pdf text-danger fs-4 me-2"></i>
                                    <div class="text-truncate small me-auto">Documento Adjunto</div>
                                    <a href="{{ $correspondencia->getFile($correspondencia->documento_arc) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <input type="file" name="documento_arc" class="form-control form-control-sm">
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem">Formatos: PDF, Word. Máx: 10MB</small>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT COMPLETO CON AJAX PARA MODO CREATE --}}
<script>
$(document).ready(function() {
    
    // 1. Inicializar Select2 para el buscador de Terceros
    if ($.fn.select2) {
        $('.select2-remitente').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Buscar nombre o código...',
            allowClear: true
        });
    }

    // 2. Lógica UX para el Switch de Finalizado (Solo aplica si existe en DOM - Modo Edición)
    const switchFinal = $('#finalizado');
    
    if (switchFinal.length > 0) {
        const labelFinal = $('#lbl_finalizado');
        const cardCierre = $('#card_cierre');
        const txtDescripcion = $('#final_descripcion');

        function updateState() {
            if(switchFinal.is(':checked')) {
                labelFinal.text('FINALIZAR RADICADO').addClass('text-success').removeClass('text-muted');
                cardCierre.addClass('active-closure');
                txtDescripcion.attr('required', true).attr('placeholder', 'Indique obligatoriamente cómo se resolvió el trámite...');
                txtDescripcion.css('border-color', '#2dce89');
            } else {
                labelFinal.text('En Proceso').addClass('text-muted').removeClass('text-success');
                cardCierre.removeClass('active-closure');
                txtDescripcion.removeAttr('required').attr('placeholder', 'Escriba aquí los detalles de la gestión realizada...');
                txtDescripcion.css('border-color', '#ced4da');
            }
        }
        
        switchFinal.on('change', updateState);
        updateState(); // Ejecutar al cargar para verificar estado inicial
    }

    // 3. Lógica AJAX para cargar TRD en Modo CREATE
    // Esta sección solo se ejecuta si NO estamos en modo edición
    @if(!$esEdicion)
        $('#flujo_id').on('change', function() {
            var flujoId = $(this).val();
            var trdSelect = $('#trd_id');

            // Limpiar y mostrar 'Cargando...'
            trdSelect.empty();
            trdSelect.append('<option value="">Cargando series...</option>');
            trdSelect.prop('disabled', true); // Mantener deshabilitado mientras carga

            if(flujoId) {
                $.ajax({
                    // URL ajustada a tu controlador
                    url: '/correspondencia/ajax/trds-por-flujo/' + flujoId, 
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        trdSelect.empty();
                        
                        if (data.length > 0) {
                            trdSelect.append('<option value="">Seleccione una serie TRD...</option>');
                            $.each(data, function(key, value) {
                                // Asegúrate que 'id_trd' y 'serie_documental' sean las columnas correctas de tu respuesta JSON
                                trdSelect.append('<option value="'+ value.id_trd +'">'+ value.serie_documental +' (Tiempo: ' + value.tiempo_gestion + ' años)</option>');
                            });
                            // ¡ESTA ES LA CLAVE! Habilitar el select para que se envíe en el POST
                            trdSelect.prop('disabled', false); 
                        } else {
                            trdSelect.append('<option value="">Sin series asociadas a este flujo</option>');
                            trdSelect.prop('disabled', true); // Se queda disabled si no hay datos, pero muestra el mensaje
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error AJAX:", error);
                        trdSelect.empty();
                        trdSelect.append('<option value="">Error al cargar TRD</option>');
                        trdSelect.prop('disabled', true);
                    }
                });
            } else {
                trdSelect.empty();
                trdSelect.append('<option value="">-- Seleccione flujo primero --</option>');
                trdSelect.prop('disabled', true);
            }
        });
        
        // Disparar change si hay un valor seleccionado (ej: si falló validación y volvió con old inputs)
        if($('#flujo_id').val()) {
            $('#flujo_id').trigger('change');
        }
    @endif
});
</script>