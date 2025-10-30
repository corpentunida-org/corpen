@csrf

@php
    // Detectar si estamos en modo edición
    $modoEdicion = isset($soporte);
@endphp

<div class="card shadow-lg border-0 rounded-4 overflow-hidden">
    <!-- Header del formulario -->
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

        <!-- Sección de Clasificación -->
        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-tags me-2"></i>Clasificación del Soporte
            </h5>
            <p class="text-muted small mb-3">Selecciona la categoría, tipo y subtipo que mejor describa tu solicitud</p>
        </div>

        <div class="row g-3 mb-4">
            <!-- Categoría de Soporte -->
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
                @error('id_categoria')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($modoEdicion)
                    <input type="hidden" name="id_categoria" value="{{ $soporte->tipo->id_categoria ?? '' }}">
                @endif
            </div>

            <!-- Tipo de Soporte -->
            <div class="col-md-4">
                <label for="id_scp_tipo" class="form-label fw-semibold">
                    <i class="bi bi-list-check me-1 text-primary"></i> Tipo <span class="text-danger">*</span>
                </label>
                <div class="form-floating">
                    <select class="form-select select2 @error('id_scp_tipo') is-invalid @enderror" 
                            id="id_scp_tipo" name="id_scp_tipo"
                            {{ $modoEdicion ? 'disabled' : '' }} required>
                            @if($modoEdicion)
                                <option selected>{{ $soporte->tipo->nombre }}</option>
                            @endif
                        <option value="">Selecciona un tipo</option>
                    </select>
                    <label for="id_scp_tipo">Tipo</label>
                </div>
                @error('id_scp_tipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($modoEdicion)
                    <input type="hidden" name="id_scp_tipo" value="{{ $soporte->id_scp_tipo ?? '' }}">
                @endif
            </div>

            <!-- Sub-Tipo de Soporte -->
            <div class="col-md-4">
                <label for="id_scp_sub_tipo" class="form-label fw-semibold">
                    <i class="bi bi-list-check me-1 text-primary"></i> Sub-Tipo <span class="text-danger">*</span>
                </label>
                <div class="form-floating">
                    <select class="form-select select2 @error('id_scp_sub_tipo') is-invalid @enderror" 
                            id="id_scp_sub_tipo" name="id_scp_sub_tipo"
                            {{ $modoEdicion ? 'disabled' : '' }} required>
                            @if($modoEdicion)
                                <option selected>{{ $soporte->subTipo->nombre }}</option>
                            @endif
                        <option value="">Selecciona un sub-tipo</option>
                    </select>
                    <label for="id_scp_sub_tipo">Sub-Tipo</label>
                </div>
                @error('id_scp_sub_tipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($modoEdicion)
                    <input type="hidden" name="id_scp_sub_tipo" value="{{ $soporte->id_scp_sub_tipo ?? '' }}">
                @endif
            </div>
        </div>

        <!-- Sección de Detalles -->
        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-text-paragraph me-2"></i>Detalles del Soporte
            </h5>
            <p class="text-muted small mb-3">Proporciona una descripción clara y detallada de tu solicitud</p>
        </div>

        <div class="row g-3 mb-4">
            <!-- Detalles del Soporte -->
            <div class="col-md-8">
                <label for="detalles_soporte" class="form-label fw-semibold">
                    <i class="bi bi-chat-left-text me-1 text-primary"></i> Descripción <span class="text-danger">*</span>
                </label>
                <textarea class="form-control @error('detalles_soporte') is-invalid @enderror" 
                          id="detalles_soporte" name="detalles_soporte" rows="4"
                          placeholder="Describe detalladamente tu solicitud..."
                          {{ $modoEdicion ? 'readonly' : '' }} required>{{ old('detalles_soporte', $soporte->detalles_soporte ?? '') }}</textarea>
                <div class="form-text">Mínimo 10 caracteres. Sé lo más específico posible para una mejor atención.</div>
                @error('detalles_soporte')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Prioridad -->
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
                @error('id_scp_prioridad')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Sección de Información del Solicitante -->
        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-person-badge me-2"></i>Información del Solicitante
            </h5>
            <p class="text-muted small mb-3">Estos datos se han autocompletado con tu información de usuario</p>
        </div>

        <div class="row g-3 mb-4">
            <!-- Creado por -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    <i class="bi bi-person-circle me-1 text-primary"></i> Solicitante
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                    <input type="hidden" name="id_users" value="{{ auth()->id() }}">
                </div>
            </div>

            <!-- Cargo / Área -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">
                    <i class="bi bi-diagram-3 me-1 text-primary"></i> Cargo / Área
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                    <input type="text" 
                        class="form-control bg-light" 
                        value="{{ $usuario->cargo->nombre_cargo ?? 'Sin cargo asignado' }}" 
                        readonly>
                    <input type="hidden" name="id_gdo_cargo" value="{{ $usuario->cargo->id ?? '' }}">
                </div>
            </div>
        </div>

        <!-- Campo oculto para asignación inicial -->
        <input type="hidden" name="cod_ter_maeTercero" value="79893305">

        <!-- Línea de Crédito (condicional) -->
        <div class="mb-4" id="linea_credito_wrapper" style="display:none;">
            <div class="card border-0 bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-credit-card text-primary me-2"></i>
                        <label for="id_cre_lineas_creditos" class="form-label fw-semibold mb-0">
                            Línea de Crédito
                        </label>
                    </div>
                    <select class="form-select select2 @error('id_cre_lineas_creditos') is-invalid @enderror" 
                            id="id_cre_lineas_creditos" name="id_cre_lineas_creditos"
                            {{ $modoEdicion ? 'disabled' : '' }}>
                        <option value="">Selecciona una línea</option>
                        @foreach ($lineas as $linea)
                            <option value="{{ $linea->id }}" 
                                {{ old('id_cre_lineas_creditos', $soporte->id_cre_lineas_creditos ?? '') == $linea->id ? 'selected' : '' }}>
                                {{ $linea->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_cre_lineas_creditos')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($modoEdicion)
                        <input type="hidden" name="id_cre_lineas_creditos" value="{{ $soporte->id_cre_lineas_creditos ?? '' }}">
                    @endif
                </div>
            </div>
        </div>

        <!-- Sección de Archivos -->
        <div class="section-divider mb-4">
            <h5 class="section-title">
                <i class="bi bi-paperclip me-2"></i>Archivos Adjuntos
            </h5>
            <p class="text-muted small mb-3">Adjunta documentos que respalden tu solicitud (PDF o imágenes)</p>
        </div>

        <div class="mb-4">
            <label for="soporte" class="form-label fw-semibold">
                <i class="bi bi-file-earmark me-1 text-primary"></i> Soporte (PDF o Imagen)
            </label>
            <div class="input-group">
                <input type="file"
                       class="form-control @error('soporte') is-invalid @enderror"
                       id="soporte"
                       name="soporte"
                       accept=".pdf,image/*">
                <label class="input-group-text" for="soporte">
                    <i class="bi bi-upload"></i>
                </label>
            </div>
            <div class="form-text">Formatos permitidos: PDF, JPG, PNG. Tamaño máximo: 5MB</div>
            @error('soporte')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if(isset($soporte) && $soporte->soporte)
            <div class="mt-3">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <div>
                        <strong>Archivo adjunto:</strong> Ya existe un archivo adjunto a este soporte.
                        <div class="mt-2">
                            <a href="{{ route('soportes.ver', $soporte->id) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary me-2">
                                <i class="bi bi-eye me-1"></i> Ver
                            </a>
                            <a href="{{ route('soportes.descargar', $soporte->id) }}" 
                               class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download me-1"></i> Descargar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Botones de acción -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('soportes.soportes.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i> {{ $modoEdicion ? 'Actualizar Soporte' : 'Guardar Soporte' }}
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
 $(document).ready(function() {
    $('.select2').select2({ 
        theme: 'bootstrap-5',
        placeholder: function() {
            return $(this).attr('placeholder');
        }
    });

    const initialSelectedTipoId = "{{ old('id_scp_tipo', $soporte->id_scp_tipo ?? '') }}";
    const initialSelectedSubTipoId = "{{ old('id_scp_sub_tipo', $soporte->id_scp_sub_tipo ?? '') }}";
    const modoEdicion = {{ isset($soporte) ? 'true' : 'false' }};

    function cargarTipos(categoriaId) {
        if (modoEdicion) return; // Evita recargar AJAX en modo edición
        let tipoSelect = $('#id_scp_tipo');
        tipoSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');
        
        if (categoriaId) {
            // Mostrar indicador de carga
            $('#id_scp_tipo').next('.select2-container').addClass('loading');
            
            let url = "{{ route('soportes.tipos.byCategoria', ':categoria') }}".replace(':categoria', categoriaId);
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    tipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un tipo</option>');
                    $.each(data, function(key, value) {
                        tipoSelect.append(`<option value="${value.id}">${value.nombre}</option>`);
                    });
                    $('#id_scp_tipo').next('.select2-container').removeClass('loading');
                },
                error: function (xhr) {
                    console.error("Error al cargar tipos:", xhr.responseText);
                    tipoSelect.prop('disabled', false).empty().append('<option value="">Error al cargar</option>');
                    $('#id_scp_tipo').next('.select2-container').removeClass('loading');
                }
            });
        } else {
            tipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un tipo</option>');
        }
    }

    function cargarSubTipos(tipoId) {
        if (modoEdicion) return; // Evita recargar AJAX en modo edición
        let subTipoSelect = $('#id_scp_sub_tipo');
        subTipoSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');
        
        if (tipoId) {
            // Mostrar indicador de carga
            $('#id_scp_sub_tipo').next('.select2-container').addClass('loading');
            
            $.ajax({
                url: "{{ route('soportes.subtipos.byTipo', ':tipo') }}".replace(':tipo', tipoId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    subTipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un sub-tipo</option>');
                    $.each(data, function(key, value) {
                        subTipoSelect.append(`<option value="${value.id}">${value.nombre}</option>`);
                    });
                    $('#id_scp_sub_tipo').next('.select2-container').removeClass('loading');
                },
                error: function(xhr) {
                    console.error("Error al cargar sub-tipos:", xhr.responseText);
                    subTipoSelect.prop('disabled', false).empty().append('<option value="">Error al cargar</option>');
                    $('#id_scp_sub_tipo').next('.select2-container').removeClass('loading');
                }
            });
        } else {
            subTipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un sub-tipo</option>');
        }
    }

    function toggleLineaCredito(subTipoId) {
        if (subTipoId == "4") {
            $('#linea_credito_wrapper').slideDown(300);
        } else {
            $('#linea_credito_wrapper').slideUp(300);
            $('#id_cre_lineas_creditos').val('').trigger('change');
        }
    }

    $('#id_categoria').on('change', function() {
        cargarTipos($(this).val());
        $('#id_scp_sub_tipo').empty().append('<option value="">Selecciona un sub-tipo</option>');
    });

    $('#id_scp_tipo').on('change', function() {
        cargarSubTipos($(this).val());
    });

    $('#id_scp_sub_tipo').on('change', function() {
        toggleLineaCredito($(this).val());
    });

    if (!modoEdicion && $('#id_categoria').val()) {
        cargarTipos($('#id_categoria').val());
    }

    if (initialSelectedSubTipoId) {
        toggleLineaCredito(initialSelectedSubTipoId);
    }
});
</script>
@endpush

@push('styles')
<style>
/* Estilos personalizados para el formulario */
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
}

.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.section-divider {
    position: relative;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-floating label {
    color: #6c757d;
}

.select2-container--bootstrap-5 .select2-selection {
    min-height: 58px;
}

.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-container.loading .select2-selection {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3e%3cpath fill='%23cccccc' d='M20.201 5.169c-8.254 0-14.946 6.692-14.946 14.946 0 8.254 6.692 14.946 14.946 14.946 8.254 0 14.946-6.692 14.946-14.946 0-8.254-6.692-14.946-14.946-14.946zm0 1.538c7.416 0 13.408 6.008 13.408 13.408 0 7.416-6.008 13.408-13.408 13.408-7.416 0-13.408-6.008-13.408-13.408 0-7.416 6.008-13.408 13.408-13.408z'/%3e%3cpath fill='%23cccccc' d='M20.201 18.538v-3.846c0-1.231-.992-2.223-2.223-2.223h-3.846c-1.231 0-2.223.992-2.223 2.223v3.846c0 1.231.992 2.223 2.223 2.223h3.846c1.231 0 2.223-.992 2.223-2.223z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 8px center;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    border-color: #86b7fe;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.form-control[readonly] {
    background-color: #f8f9fa;
    opacity: 1;
}

@media (max-width: 768px) {
    .section-title {
        font-size: 1rem;
    }
    
    .icon-box {
        width: 50px;
        height: 50px;
    }
    
    .card-header {
        padding: 1.5rem !important;
    }
}
</style>
@endpush