@php
    $esEdicion = isset($correspondencia);
    $fechaVal = old('fecha_solicitud', $esEdicion ? $correspondencia->fecha_solicitud->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
@endphp

<style>
    /* --- ESTILOS DE ESTRUCTURA Y UX --- */
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
    }

    .resolution-card {
        background: linear-gradient(145deg, #ffffff, #f4f7f6);
        border-left: 5px solid #5e72e4;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .resolution-card.active-closure {
        border-left-color: #2dce89;
        background: #f6fff9;
    }

    .form-switch-lg .form-check-input {
        height: 1.5rem; width: 3rem; cursor: pointer;
    }
    
    .icon-box {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; background-color: #e9ecef; color: #525f7f;
        margin-right: 15px;
    }

    /* --- ESTILOS MEJORADOS DE DRAG & DROP --- */
    .drop-zone {
        position: relative;
        border: 2px dashed #ced4da;
        border-radius: 8px;
        padding: 25px 20px;
        text-align: center;
        background-color: #f8f9fe;
        transition: all 0.3s ease;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .drop-zone.dragover {
        border-color: #5e72e4;
        background-color: #f0f3ff;
        transform: scale(1.02);
    }

    .drop-zone-input {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .preview-container {
        display: none;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 10px;
        width: 100%;
        z-index: 20;
        position: relative;
    }

    /* --- ESTILOS DE LA SECCIÓN DE DATOS DEL REMITENTE --- */
    .remitente-data-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-top: 1rem;
        border: 1px solid #e9ecef;
    }

    .remitente-data-header {
        background: linear-gradient(135deg, #5e72e4 0%, #4c63d2 100%);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem 0.5rem 0 0;
        margin: -1rem -1rem 1rem -1rem;
    }

    .data-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .data-item i {
        color: #5e72e4;
        margin-right: 0.75rem;
        margin-top: 2px;
        font-size: 0.9rem;
    }

    .data-item-content {
        flex-grow: 1;
    }

    .data-item-label {
        font-size: 0.7rem;
        color: #8898aa;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .data-item-value {
        font-size: 0.9rem;
        color: #32325d;
        font-weight: 500;
    }

    .loading-spinner {
        text-align: center;
        padding: 2rem;
    }

    .error-message {
        padding: 1rem;
    }
</style>

<div class="container-fluid py-2">
    
    {{-- SECCIÓN 1: ENCABEZADO E INFORMACIÓN GENERAL --}}
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
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <label class="field-label">ID Radicado</label>
                    @if($esEdicion)
                        <div class="static-value">{{ $correspondencia->id_radicado }}</div>
                        <input type="hidden" name="id_radicado" value="{{ $correspondencia->id_radicado }}">
                    @else
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">RAD-</span>
                            <input type="text" name="id_radicado" class="form-control fw-bold border-start-0 ps-0" 
                                   value="{{ old('id_radicado', $siguienteId) }}" readonly>
                        </div>
                    @endif
                </div>

                <div class="col-md-3">
                    <label class="field-label">Fecha Solicitud</label>
                    <div class="{{ $esEdicion ? 'static-value' : '' }}">
                        @if($esEdicion)
                            <i class="bi bi-calendar-event me-1 text-muted"></i> {{ $correspondencia->fecha_solicitud->format('d/m/Y h:i A') }}
                        @else
                            <input type="datetime-local" class="form-control" value="{{ $fechaVal }}" readonly>
                        @endif
                        <input type="hidden" name="fecha_solicitud" value="{{ $fechaVal }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="field-label">Medio Recepción</label>
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
                
                <div class="col-md-3">
                    <label class="field-label">Estado</label>
                    @if($esEdicion)
                        <div class="static-value"><span class="badge bg-secondary text-dark border">{{ $correspondencia->estado->nombre ?? 'N/A' }}</span></div>
                        <input type="hidden" name="estado_id" value="{{ $correspondencia->estado_id }}">
                    @else
                        <select name="estado_id" class="form-select" required>
                            @foreach($estados as $e)
                                <option value="{{ $e->id }}" {{ $e->id == 3 ? 'selected' : '' }}>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- COLUMNA IZQUIERDA: CONTENIDO --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="field-label text-primary mb-3 border-bottom pb-2"><i class="bi bi-text-paragraph"></i> Contenido</h6>
                    <div class="mb-4">
                        <label class="field-label">Asunto <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="static-value fw-bold">{{ $correspondencia->asunto }}</div>
                            <input type="hidden" name="asunto" value="{{ $correspondencia->asunto }}">
                        @else
                            <input type="text" name="asunto" class="form-control" value="{{ old('asunto') }}" required>
                        @endif
                    </div>
                    <div class="mb-2">
                        <label class="field-label">Observación</label>
                        @if($esEdicion)
                            <div class="p-3 bg-light rounded text-muted small border">{{ $correspondencia->observacion_previa ?? 'Sin observaciones.' }}</div>
                            <input type="hidden" name="observacion_previa" value="{{ $correspondencia->observacion_previa }}">
                        @else
                            <textarea name="observacion_previa" class="form-control" rows="3">{{ old('observacion_previa') }}</textarea>
                        @endif
                    </div>
                </div>
            </div>

            @if($esEdicion)
            <div class="card resolution-card mb-4" id="card_cierre">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="fw-bold text-dark"><i class="bi bi-pen-fill me-2"></i>Cierre de Radicado</h5>
                        </div>
                        <div class="form-check form-switch form-switch-lg">
                            <input class="form-check-input" type="checkbox" name="finalizado" id="finalizado" value="1" {{ old('finalizado', $correspondencia->finalizado) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-2 mt-1" for="finalizado" id="lbl_finalizado">En Proceso</label>
                        </div>
                    </div>
                    <textarea name="final_descripcion" id="final_descripcion" class="form-control" rows="5" placeholder="Gestión final...">{{ old('final_descripcion', $correspondencia->final_descripcion) }}</textarea>
                </div>
            </div>
            @endif
        </div>

        {{-- COLUMNA DERECHA: CLASIFICACIÓN Y ARCHIVO --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h6 class="field-label text-primary mb-3 border-bottom pb-2"><i class="bi bi-sliders"></i> Clasificación</h6>

                    <div class="mb-4">
                        <label class="field-label">Remitente <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="static-value fw-bold">{{ $correspondencia->remitente->cod_ter ?? '' }} - {{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</div>
                            <input type="hidden" name="remitente_id" id="remitente_id" value="{{ $correspondencia->remitente_id }}">
                        @else
                            <select name="remitente_id" id="remitente_id" class="form-select select2-remitente" required>
                                <option value="">Buscar por nombre o cédula...</option>
                                @foreach($remitentes as $r)
                                    {{-- Agregamos la cédula (cod_ter) al texto visible --}}
                                    <option value="{{ $r->cod_ter }}">{{ $r->cod_ter }} - {{ $r->nom_ter }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- SECCIÓN DE DATOS DEL REMITENTE --}}
                    <div class="remitente-data-card" id="remitente-data-card" style="display: none;">
                        <div class="remitente-data-header">
                            <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Datos del Remitente</h6>
                        </div>
                        
                        <div id="remitente-data-content">
                            <!-- El contenido se cargará aquí dinámicamente -->
                        </div>

                        {{-- BOTÓN PARA ACTUALIZAR TERCERO --}}
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" id="btn-actualizar-tercero" class="btn btn-sm btn-outline-primary" style="display: none;">
                                <i class="bi bi-pencil-square me-1"></i> Actualizar Tercero
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="field-label">Flujo / TRD <span class="text-danger">*</span></label>
                        @if($esEdicion)
                            <div class="static-value small">{{ $correspondencia->flujo->nombre }} / {{ $correspondencia->trd->serie_documental }}</div>
                        @else
                            <select name="flujo_id" id="flujo_id" class="form-select mb-2" required>
                                <option value="">Seleccione flujo...</option>
                                @foreach($flujos as $f)
                                    <option value="{{ $f->id }}">{{ $f->nombre }}</option>
                                @endforeach
                            </select>
                            <select name="trd_id" id="trd_id" class="form-select" required disabled>
                                <option value="">-- Seleccione flujo primero --</option>
                            </select>
                        @endif
                    </div>

                    {{-- CONFIDENCIALIDAD --}}
                    <div class="mb-4 pt-3 border-top d-flex justify-content-between">
                        <label class="field-label">¿Confidencial?</label>
                        @if($esEdicion)
                            <span class="badge {{ $correspondencia->es_confidencial ? 'bg-warning text-dark' : 'bg-light border' }}">
                                {{ $correspondencia->es_confidencial ? 'SÍ' : 'NO' }}
                            </span>
                        @else
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="es_confidencial" value="1" checked>
                            </div>
                        @endif
                    </div>

                    {{-- ZONA DE ARCHIVO: DRAG & DROP + CLIC NATIVO --}}
                    <div class="mt-4 pt-3 border-top">
                        <label class="field-label">Documento Digital</label>
                        @if($esEdicion && $correspondencia->documento_arc)
                            <div class="card bg-light border p-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-pdf text-danger fs-4 me-2"></i>
                                    <div class="text-truncate small me-auto">Ver adjunto</div>
                                    <a href="{{ $correspondencia->getFile($correspondencia->documento_arc) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div id="dropzone" class="drop-zone">
                                <input type="file" name="documento_arc" id="file-input" class="drop-zone-input" accept=".pdf,.doc,.docx">
                                
                                <div id="dropzone-prompt">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-primary"></i>
                                    <p class="mb-0 small fw-bold">Arrastra o haz clic aquí</p>
                                </div>

                                <div id="file-preview" class="preview-container">
                                    <div class="d-flex align-items-center">
                                        <i id="preview-icon" class="bi bi-file-earmark-text fs-4 me-2"></i>
                                        <div class="text-truncate small me-auto text-start">
                                            <span id="file-name" class="d-block fw-bold text-dark"></span>
                                        </div>
                                        <button type="button" id="remove-file" class="btn btn-sm text-danger" style="z-index: 30;">
                                            <i class="bi bi-x-circle-fill fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
 $(document).ready(function() {
    // 1. SELECT2
    if ($.fn.select2) {
        $('.select2-remitente').select2({ theme: 'bootstrap-5', width: '100%' });
    }

    // 2. LÓGICA SWITCH CIERRE
    const switchFinal = $('#finalizado');
    if (switchFinal.length > 0) {
        const updateState = () => {
            const isChecked = switchFinal.is(':checked');
            $('#lbl_finalizado').text(isChecked ? 'FINALIZAR RADICADO' : 'En Proceso').toggleClass('text-success', isChecked);
            $('#card_cierre').toggleClass('active-closure', isChecked);
            $('#final_descripcion').attr('required', isChecked);
        };
        switchFinal.on('change', updateState);
        updateState();
    }

    // 3. AJAX TRD
    @if(!$esEdicion)
    $('#flujo_id').on('change', function() {
        let id = $(this).val();
        let trd = $('#trd_id');
        trd.empty().append('<option>Cargando...</option>').prop('disabled', true);
        if(id) {
            $.getJSON('/correspondencia/ajax/trds-por-flujo/' + id, function(data) {
                trd.empty().append('<option value="">Seleccione serie...</option>');
                data.forEach(i => trd.append(`<option value="${i.id_trd}">${i.serie_documental}</option>`));
                trd.prop('disabled', false);
            });
        }
    });
    @endif

    // 4. LÓGICA DRAG & DROP + CLICK
    const fileInput = $('#file-input');
    const dropzone = $('#dropzone');

    fileInput.on('change', function() {
        if (this.files && this.files[0]) {
            let file = this.files[0];
            
            if (file.size > 10 * 1024 * 1024) {
                alert("El archivo supera los 10MB");
                this.value = '';
                return;
            }

            let ext = file.name.split('.').pop().toLowerCase();
            let iconClass = ext === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-word text-primary';
            
            $('#preview-icon').attr('class', `bi ${iconClass} fs-4 me-2`);
            $('#file-name').text(file.name);
            $('#dropzone-prompt').hide();
            $('#file-preview').show();
        }
    });

    dropzone.on('dragover', function() { $(this).addClass('dragover'); });
    dropzone.on('dragleave drop', function() { $(this).removeClass('dragover'); });

    $('#remove-file').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.val('');
        $('#file-preview').hide();
        $('#dropzone-prompt').show();
    });

    // 5. LÓGICA PARA CARGAR DATOS DEL REMITENTE
    const CLIENT_DATA_URL = "{{ route('correspondencia.ajax.remitente.codigo', 'PLACEHOLDER') }}".replace('PLACEHOLDER', '');
    
    // --- INICIO: NUEVA LÓGICA PARA ACTUALIZAR TERCERO ---
    const EDITAR_TERCERO_URL = "{{ route('maestras.terceros.edit', 'ID_PARA_REEMPLAZAR') }}";

    // Modificamos la función renderRemitenteData para mostrar el botón
    function renderRemitenteData(data) {
        const $content = $('#remitente-data-content');
        let html = `
            <div class="data-item">
                <i class="bi bi-person-fill"></i>
                <div class="data-item-content">
                    <div class="data-item-label">Nombre</div>
                    <div class="data-item-value">${data.nom_ter || 'N/A'}</div>
                </div>
            </div>           
        `;

        if (data.dir) {
            html += `
                <div class="data-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <div class="data-item-content">
                        <div class="data-item-label">Dirección</div>
                        <div class="data-item-value">${data.dir}</div>
                    </div>
                </div>
            `;
        }

        if (data.tel) {
            html += `
                <div class="data-item">
                    <i class="bi bi-telephone-fill"></i>
                    <div class="data-item-content">
                        <div class="data-item-label">Teléfono</div>
                        <div class="data-item-value">${data.tel}</div>
                    </div>
                </div>
            `;
        }
        
        if (data.email) {
            html += `
                <div class="data-item">
                    <i class="bi bi-envelope-fill"></i>
                    <div class="data-item-content">
                        <div class="data-item-label">Email</div>
                        <div class="data-item-value">${data.email}</div>
                    </div>
                </div>
            `;
        }
        
        if (data.cod_dist) {
            html += `
                <div class="data-item">
                    <i class="bi bi-building"></i>
                    <div class="data-item-content">
                        <div class="data-item-label">Distrito</div>
                        <div class="data-item-value">${data.cod_dist}</div>
                    </div>
                </div>
            `;
        }

        $content.html(html);
        $('#btn-actualizar-tercero').show(); // <-- MOSTRAR EL BOTÓN
    }

    // Modificamos la función loadClientData para ocultar el botón mientras carga
    function loadClientData(codTer) {
        const $card = $('#remitente-data-card');
        const $content = $('#remitente-data-content');
        
        if (!codTer) {
            $card.hide();
            $('#btn-actualizar-tercero').hide(); // <-- OCULTAR BOTÓN SI NO HAY REMITENTE
            return;
        }
        
        $card.show();
        $('#btn-actualizar-tercero').hide(); // <-- OCULTAR BOTÓN MIENTRAS CARGA
        $content.html(`
            <div class="loading-spinner">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <p class="mb-0 mt-2 small text-muted">Cargando datos...</p>
            </div>
        `);
        
        $.ajax({
            url: CLIENT_DATA_URL + codTer,
            type: 'GET',
            dataType: 'json',
            timeout: 10000
        })
        .done(function(data) {
            renderRemitenteData(data);
        })
        .fail(function(xhr) {
            $content.html(`
                <div class="alert alert-danger small py-2 error-message">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Error al cargar datos: ${xhr.responseJSON?.message || 'Intente nuevamente'}
                </div>
            `);
        });
    }

    // Evento para abrir la pestaña de edición
    $('#btn-actualizar-tercero').on('click', function() {
        // Obtenemos el valor del select de remitente (que es el cod_ter)
        const codTer = $('#remitente_id').val(); 
        
        if (codTer) {
            // Reemplazamos el marcador por el ID real seleccionado
            const urlFinal = EDITAR_TERCERO_URL.replace('ID_PARA_REEMPLAZAR', codTer);
            
            // Abrimos en una nueva pestaña
            window.open(urlFinal, '_blank');
        } else {
            alert('Por favor, seleccione un remitente primero para poder editarlo.');
        }
    });

    // Listener para refrescar los datos si el tercero se actualiza en la otra pestaña
    window.addEventListener('storage', function(event) {
        if (event.key === 'tercero_actualizado' && event.newValue) {
            const codTerActualizado = event.newValue;
            if (codTerActualizado === $('#remitente_id').val()) {
                loadClientData(codTerActualizado);
            }
            localStorage.removeItem('tercero_actualizado');
        }
    });
    // --- FIN: NUEVA LÓGICA ---

    // Evento para cargar datos cuando cambia el remitente
    $('#remitente_id').on('change', function() {
        const codTer = $(this).val();
        loadClientData(codTer);
    });

    // Cargar datos iniciales si hay un remitente seleccionado (modo edición)
    @if($esEdicion && isset($correspondencia->remitente_id))
        loadClientData('{{ $correspondencia->remitente_id }}');
    @endif
});
</script>