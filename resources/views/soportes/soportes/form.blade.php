@push('styles')
{{-- SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
/* Estilos Generales */
.bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); }
.icon-box { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; }
.section-divider { position: relative; padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid #e9ecef; }
.section-title { font-size: 1.1rem; font-weight: 600; color: #495057; margin-bottom: 0.5rem; }
.form-floating label { color: #6c757d; }
.select2-container--bootstrap-5 .select2-selection { min-height: 58px; }
.select2-container--bootstrap-5.select2-container--focus .select2-selection { border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
.input-group-text { background-color: #f8f9fa; border-right: none; }
.form-control:focus, .form-select:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); border-color: #86b7fe; }
.btn-primary { background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border: none; }
.btn-primary:hover { background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%); transform: translateY(-1px); box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3); }
.card { transition: all 0.3s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
.form-control[readonly] { background-color: #f8f9fa; opacity: 1; }

/* --- ESTILOS DRAG & DROP --- */
.upload-area {
    border: 2px dashed #cbd5e1;
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.upload-area:hover, .upload-area.dragover {
    background-color: #eef2ff;
    border-color: #0d6efd;
    transform: scale(1.01);
}

.upload-area .icon {
    font-size: 3rem;
    color: #64748b;
    margin-bottom: 1rem;
    transition: color 0.3s;
}

.upload-area:hover .icon, .upload-area.dragover .icon {
    color: #0d6efd;
}

.upload-area input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.file-info {
    display: none; /* Se muestra con JS */
    margin-top: 1rem;
    padding: 0.5rem 1rem;
    background: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
@endpush

@php
    $modoEdicion = isset($soporte);
@endphp

<div class="card shadow-lg border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-gradient-primary text-white p-4">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="icon-box bg-white bg-opacity-20 rounded-3 p-3 me-3">
                    <i class="bi bi-headset fs-4"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-bold">{{ $modoEdicion ? 'Editar Soporte' : 'Nuevo Soporte' }}</h4>
                <p class="mb-0 opacity-75 small">{{ $modoEdicion ? 'Modifica la información del soporte existente' : 'Completa el formulario para crear un nuevo soporte' }}</p>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        {{-- Radicado --}}
        @if(!$modoEdicion)
        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <div>
                <strong>Radicado asignado:</strong> <span class="fw-bold">{{ $proximoId }}</span>
                <input type="hidden" name="proximo_id" value="{{ $proximoId }}">
            </div>
        </div>
        @endif

        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-tags me-2"></i>Clasificación del Soporte
            </h5>
            <p class="text-muted small mb-3">Selecciona la categoría, tipo y subtipo que mejor describa tu solicitud</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="id_categoria" class="form-label fw-semibold">
                    <i class="bi bi-folder me-1 text-primary"></i> Categoría <span class="text-danger">*</span>
                </label>
                <div class="form-floating">
                    <select class="form-select select2 @error('id_categoria') is-invalid @enderror" 
                            id="id_categoria" name="id_categoria"
                            {{ $modoEdicion ? 'disabled' : '' }} required>
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" 
                                {{ old('id_categoria', $soporte->tipo->id_categoria ?? '') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <label for="id_categoria">Categoría</label>
                </div>
                @error('id_categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if($modoEdicion) <input type="hidden" name="id_categoria" value="{{ $soporte->tipo->id_categoria ?? '' }}"> @endif
            </div>

            <div class="col-md-4">
                <label for="id_scp_tipo" class="form-label fw-semibold">
                    <i class="bi bi-list-check me-1 text-primary"></i> Tipo <span class="text-danger">*</span>
                </label>
                <div class="form-floating">
                    <select class="form-select select2 @error('id_scp_tipo') is-invalid @enderror" 
                            id="id_scp_tipo" name="id_scp_tipo"
                            {{ $modoEdicion ? 'disabled' : '' }} required>
                            @if($modoEdicion) <option selected>{{ $soporte->tipo->nombre }}</option> @endif
                        <option value="">Selecciona un tipo</option>
                    </select>
                    <label for="id_scp_tipo">Tipo</label>
                </div>
                @error('id_scp_tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if($modoEdicion) <input type="hidden" name="id_scp_tipo" value="{{ $soporte->id_scp_tipo ?? '' }}"> @endif
            </div>

            <div class="col-md-4">
                <label for="id_scp_sub_tipo" class="form-label fw-semibold">
                    <i class="bi bi-list-check me-1 text-primary"></i> Sub-Tipo <span class="text-danger">*</span>
                </label>
                <div class="form-floating">
                    <select class="form-select select2 @error('id_scp_sub_tipo') is-invalid @enderror" 
                            id="id_scp_sub_tipo" name="id_scp_sub_tipo"
                            {{ $modoEdicion ? 'disabled' : '' }} required>
                            @if($modoEdicion) <option selected>{{ $soporte->subTipo->nombre }}</option> @endif
                        <option value="">Selecciona un sub-tipo</option>
                    </select>
                    <label for="id_scp_sub_tipo">Sub-Tipo</label>
                </div>
                @error('id_scp_sub_tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if($modoEdicion) <input type="hidden" name="id_scp_sub_tipo" value="{{ $soporte->id_scp_sub_tipo ?? '' }}"> @endif
            </div>
        </div>

        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-text-paragraph me-2"></i>Detalles del Soporte
            </h5>
            <p class="text-muted small mb-3">Proporciona una descripción clara y detallada de tu solicitud</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <label for="detalles_soporte" class="form-label fw-semibold">
                    <i class="bi bi-chat-left-text me-1 text-primary"></i> Descripción <span class="text-danger">*</span>
                </label>
                <textarea class="form-control @error('detalles_soporte') is-invalid @enderror" 
                          id="detalles_soporte" name="detalles_soporte" rows="4"
                          placeholder="Describe detalladamente tu solicitud..."
                          {{ $modoEdicion ? 'readonly' : '' }} required>{{ old('detalles_soporte', $soporte->detalles_soporte ?? '') }}</textarea>
                <div class="form-text">Mínimo 10 caracteres. Sé lo más específico posible.</div>
                @error('detalles_soporte') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="id_scp_prioridad" class="form-label fw-semibold">
                    <i class="bi bi-flag me-1 text-primary"></i> Prioridad <span class="text-danger">*</span>
                </label>
                <div class="form-floating">
                    <select class="form-select select2 @error('id_scp_prioridad') is-invalid @enderror" 
                            id="id_scp_prioridad" name="id_scp_prioridad" required>
                        <option value="">Selecciona una prioridad</option>
                        @foreach ($prioridades as $prioridad)
                            <option value="{{ $prioridad->id }}" 
                                {{ old('id_scp_prioridad', $soporte->id_scp_prioridad ?? '') == $prioridad->id ? 'selected' : '' }}>
                                {{ $prioridad->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <label for="id_scp_prioridad">Prioridad</label>
                </div>
                @error('id_scp_prioridad') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-person-badge me-2"></i>Información del Solicitante
            </h5>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold"><i class="bi bi-person-circle me-1 text-primary"></i> Solicitante</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                    <input type="hidden" name="id_users" value="{{ auth()->id() }}">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold"><i class="bi bi-diagram-3 me-1 text-primary"></i> Cargo / Área</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                    <input type="text" class="form-control bg-light" value="{{ $usuario->cargo->nombre_cargo ?? 'Sin cargo asignado' }}" readonly>
                    <input type="hidden" name="id_gdo_cargo" value="{{ $usuario->cargo->id ?? '' }}">
                </div>
            </div>
        </div>

        <input type="hidden" name="cod_ter_maeTercero" value="79893305">

        <div class="mb-4" id="linea_credito_wrapper" style="display:none;">
            <div class="card border-0 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-credit-card text-primary me-2"></i>
                        <label for="id_cre_lineas_creditos" class="form-label fw-semibold mb-0">Línea de Crédito</label>
                    </div>
                    <select class="form-select select2" id="id_cre_lineas_creditos" name="id_cre_lineas_creditos" {{ $modoEdicion ? 'disabled' : '' }}>
                        <option value="">Selecciona una línea</option>
                        @foreach ($lineas as $linea)
                            <option value="{{ $linea->id }}" {{ old('id_cre_lineas_creditos', $soporte->id_cre_lineas_creditos ?? '') == $linea->id ? 'selected' : '' }}>{{ $linea->nombre }}</option>
                        @endforeach
                    </select>
                    @if($modoEdicion) <input type="hidden" name="id_cre_lineas_creditos" value="{{ $soporte->id_cre_lineas_creditos ?? '' }}"> @endif
                </div>
            </div>
        </div>

        <div class="section-divider mb-4">
            <h5 class="section-title"><i class="bi bi-paperclip me-2"></i>Archivos Adjuntos</h5>
            <p class="text-muted small mb-3">Adjunta documentos (PDF o imágenes) arrastrándolos al recuadro.</p>
        </div>

        <div class="mb-4">
            <div class="upload-area" id="uploadArea">
                <div class="icon-container">
                    <i class="bi bi-cloud-arrow-up icon"></i>
                </div>
                <h5 class="fw-bold text-secondary mb-2">Arrastra y suelta tu archivo aquí</h5>
                <p class="text-muted small mb-0">o haz clic para buscar en tu equipo</p>
                <p class="text-muted small mt-2 fst-italic">(Máx: 5MB - PDF, JPG, PNG)</p>
                
                <input type="file" id="soporte" name="soporte" accept=".pdf,image/*">
                
                <div id="fileInfo" class="file-info" style="display: none;">
                    <i class="bi bi-file-earmark-check text-success fs-5"></i>
                    <span id="fileName" class="fw-semibold text-dark">nombre_archivo.pdf</span>
                </div>
            </div>
            @error('soporte') <div class="text-danger small mt-2">{{ $message }}</div> @enderror

            @if(isset($soporte) && $soporte->soporte)
            <div class="mt-3">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <div>
                        <strong>Archivo existente:</strong> Ya existe un soporte adjunto.
                        <div class="mt-2">
                            <a href="{{ route('soportes.ver', $soporte->id) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-eye me-1"></i> Ver</a>
                            <a href="{{ route('soportes.descargar', $soporte->id) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-download me-1"></i> Descargar</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('soportes.soportes.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-4" id="btnGuardarSoporte">
                <i class="bi bi-save me-1"></i> {{ $modoEdicion ? 'Actualizar Soporte' : 'Guardar Soporte' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap-5', placeholder: function() { return $(this).attr('placeholder'); } });

    // ==========================================
    // LÓGICA DRAG & DROP
    // ==========================================
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('soporte');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const icon = uploadArea.querySelector('.icon');

    // Eventos para arrastrar
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        uploadArea.classList.add('dragover');
    }

    function unhighlight(e) {
        uploadArea.classList.remove('dragover');
    }

    // Manejar el Drop (Soltar archivo)
    uploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files; // Asignar archivos al input oculto
        updateUI(files);
    }

    // Manejar clic normal (Input Change)
    fileInput.addEventListener('change', function() {
        updateUI(this.files);
    });

    function updateUI(files) {
        if (files.length > 0) {
            fileName.textContent = files[0].name;
            fileInfo.style.display = 'inline-flex';
            icon.classList.remove('bi-cloud-arrow-up');
            icon.classList.add('bi-check-circle-fill', 'text-success');
        } else {
            fileInfo.style.display = 'none';
        }
    }
    // ==========================================

    // ==========================================
    // SEGURIDAD: ANTI-DOBLE CLICK Y MODAL
    // ==========================================
    $('#btnGuardarSoporte').closest('form').on('submit', function(e) {
        var form = this;
        var btn = $('#btnGuardarSoporte');

        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor, diligencia los campos obligatorios.',
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        e.preventDefault();
        btn.prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');

        Swal.fire({
            title: 'Procesando...',
            html: '<p>Estamos guardando la información.</p><small class="text-muted">Por favor espere.</small>',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                form.submit();
            }
        });
    });

    // ==========================================
    // LOGICA AJAX (TIPOS / SUBTIPOS)
    // ==========================================
    const initialSelectedTipoId = "{{ old('id_scp_tipo', $soporte->id_scp_tipo ?? '') }}";
    const initialSelectedSubTipoId = "{{ old('id_scp_sub_tipo', $soporte->id_scp_sub_tipo ?? '') }}";
    const modoEdicion = {{ isset($soporte) ? 'true' : 'false' }};

    function cargarTipos(categoriaId) {
        if (modoEdicion) return; 
        let tipoSelect = $('#id_scp_tipo');
        tipoSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');
        
        if (categoriaId) {
            $('#id_scp_tipo').next('.select2-container').addClass('loading');
            let url = "{{ route('soportes.tipos.byCategoria', ':categoria') }}".replace(':categoria', categoriaId);
            $.ajax({
                url: url, type: 'GET', dataType: 'json',
                success: function(data) {
                    tipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un tipo</option>');
                    $.each(data, function(key, value) { tipoSelect.append(`<option value="${value.id}">${value.nombre}</option>`); });
                    $('#id_scp_tipo').next('.select2-container').removeClass('loading');
                },
                error: function () {
                    tipoSelect.prop('disabled', false).empty().append('<option value="">Error</option>');
                    $('#id_scp_tipo').next('.select2-container').removeClass('loading');
                }
            });
        } else { tipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un tipo</option>'); }
    }

    function cargarSubTipos(tipoId) {
        if (modoEdicion) return;
        let subTipoSelect = $('#id_scp_sub_tipo');
        subTipoSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');
        
        if (tipoId) {
            $('#id_scp_sub_tipo').next('.select2-container').addClass('loading');
            $.ajax({
                url: "{{ route('soportes.subtipos.byTipo', ':tipo') }}".replace(':tipo', tipoId),
                type: 'GET', dataType: 'json',
                success: function(data) {
                    subTipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un sub-tipo</option>');
                    $.each(data, function(key, value) { subTipoSelect.append(`<option value="${value.id}">${value.nombre}</option>`); });
                    $('#id_scp_sub_tipo').next('.select2-container').removeClass('loading');
                },
                error: function() {
                    subTipoSelect.prop('disabled', false).empty().append('<option value="">Error</option>');
                    $('#id_scp_sub_tipo').next('.select2-container').removeClass('loading');
                }
            });
        } else { subTipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un sub-tipo</option>'); }
    }

    function toggleLineaCredito(subTipoId) {
        if (subTipoId == "4") { $('#linea_credito_wrapper').slideDown(300); } 
        else { $('#linea_credito_wrapper').slideUp(300); $('#id_cre_lineas_creditos').val('').trigger('change'); }
    }

    $('#id_categoria').on('change', function() { cargarTipos($(this).val()); $('#id_scp_sub_tipo').empty().append('<option value="">Selecciona un sub-tipo</option>'); });
    $('#id_scp_tipo').on('change', function() { cargarSubTipos($(this).val()); });
    $('#id_scp_sub_tipo').on('change', function() { toggleLineaCredito($(this).val()); });

    if (!modoEdicion && $('#id_categoria').val()) { cargarTipos($('#id_categoria').val()); }
    if (initialSelectedSubTipoId) { toggleLineaCredito(initialSelectedSubTipoId); }
});
</script>
@endpush