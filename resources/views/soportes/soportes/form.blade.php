@csrf

<div class="card shadow-lg border-0 rounded-3 p-4">
    <h4 class="mb-4 text-primary fw-bold">
        <i class="bi bi-headset me-2"></i> {{ isset($soporte) ? '' : '' }}
    </h4>

    <!-- Categoría de Soporte -->
    <div class="mb-3">
        <label for="id_categoria" class="form-label fw-semibold">
            <i class="bi bi-folder me-1"></i> Categoría de Soporte
        </label>
        <select class="form-select select2 @error('id_categoria') is-invalid @enderror" 
                id="id_categoria" name="id_categoria" required>
            <option value="">Selecciona una categoría</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" 
                    {{ old('id_categoria', $soporte->tipo->id_categoria ?? '') == $categoria->id ? 'selected' : '' }}>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_categoria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Tipo de Soporte -->
    <div class="mb-3">
        <label for="id_scp_tipo" class="form-label fw-semibold">
            <i class="bi bi-list-check me-1"></i> Tipo de Soporte
        </label>
        <select class="form-select select2 @error('id_scp_tipo') is-invalid @enderror" 
                id="id_scp_tipo" name="id_scp_tipo" required>
            <option value="">Selecciona un tipo</option>
        </select>
        @error('id_scp_tipo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Sub-Tipo de Soporte -->
    <div class="mb-3">
        <label for="id_scp_sub_tipo" class="form-label fw-semibold">
            <i class="bi bi-list-check me-1"></i> Sub-Tipo de Soporte
        </label>
        <select class="form-select select2 @error('id_scp_sub_tipo') is-invalid @enderror" 
                id="id_scp_sub_tipo" name="id_scp_sub_tipo" required>
            <option value="">Selecciona un sub-tipo</option>
        </select>
        @error('id_scp_sub_tipo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Detalles del Soporte -->
    <div class="mb-3">
        <label for="detalles_soporte" class="form-label fw-semibold">
            <i class="bi bi-chat-left-text me-1"></i> Detalles del Soporte
        </label>
        <textarea class="form-control @error('detalles_soporte') is-invalid @enderror" 
                  id="detalles_soporte" name="detalles_soporte" rows="3" required>{{ old('detalles_soporte', $soporte->detalles_soporte ?? '') }}</textarea>
        @error('detalles_soporte')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Prioridad -->
    <div class="mb-3">
        <label for="id_scp_prioridad" class="form-label fw-semibold">
            <i class="bi bi-flag me-1"></i> Prioridad
        </label>
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
        @error('id_scp_prioridad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Creado por -->
    <div class="mb-3">
        <label class="form-label fw-semibold">
            <i class="bi bi-person-circle me-1"></i> Creado por
        </label>
        <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
        <input type="hidden" name="id_users" value="{{ auth()->id() }}">
    </div>

    <!-- Cargo / Área -->
    <div class="mb-3">
        <label class="form-label fw-semibold">
            <i class="bi bi-diagram-3 me-1"></i> Cargo
        </label>
        <input type="text" 
            class="form-control bg-light" 
            value="{{ $usuario->cargo->nombre_cargo ?? 'Sin cargo asignado' }}" 
            readonly>
        <input type="hidden" name="id_gdo_cargo" value="{{ $usuario->cargo->id ?? '' }}">
    </div>

    <!-- Asignación Inicial -->
    <div class="mb-3">
        <label class="form-label fw-semibold">
            <i class="bi bi-person-badge me-1"></i> Asignación Inicial
        </label>
        <input type="text" class="form-control bg-light" 
            value="{{ $terceros->where('cod_ter', '79893305')->first()->nom_ter ?? 'Usuario 79893305' }}" 
            readonly disabled>
        <input type="hidden" name="cod_ter_maeTercero" value="79893305">
    </div>

    <!-- Línea de Crédito -->
    <div class="mb-3" id="linea_credito_wrapper" style="display:none;">
        <label for="id_cre_lineas_creditos" class="form-label fw-semibold">
            <i class="bi bi-credit-card me-1"></i> Línea Crédito
        </label>
        <select class="form-select select2 @error('id_cre_lineas_creditos') is-invalid @enderror" 
                id="id_cre_lineas_creditos" name="id_cre_lineas_creditos">
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
    </div>

    <!-- Archivo de Soporte -->
    <div class="mb-3">
        <label for="soporte" class="form-label fw-semibold">
            <i class="bi bi-paperclip me-1"></i> Soporte (PDF o Imagen)
        </label>
        <input type="file" class="form-control @error('soporte') is-invalid @enderror" 
               id="soporte" name="soporte" accept=".pdf,image/*">
        @error('soporte')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if(isset($soporte) && $soporte->soporte)
            <div class="mt-2">
                <a href="{{ asset('storage/soportes/'.$soporte->soporte) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    Ver soporte actual
                </a>
            </div>
        @endif
    </div>

    <!-- Botón -->
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-gradient px-4 py-2">
            <i class="bi bi-save me-1"></i> {{ isset($soporte) ? 'Actualizar Soporte' : 'Guardar Soporte' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({ theme: 'bootstrap-5' });

    const initialSelectedTipoId = "{{ old('id_scp_tipo', $soporte->id_scp_tipo ?? '') }}";
    const initialSelectedSubTipoId = "{{ old('id_scp_sub_tipo', $soporte->id_scp_sub_tipo ?? '') }}";

    function cargarTipos(categoriaId) {
        let tipoSelect = $('#id_scp_tipo');
        tipoSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');
        if (categoriaId) {          
            let url = "{{ route('soportes.tipos.byCategoria', ':categoria') }}";
            url = url.replace(':categoria', categoriaId);

        console.log("Llamando a URL:", url); // 🔍 depuración  
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    tipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un tipo</option>');
                    $.each(data, function(key, value) {
                        let selected = (value.id == initialSelectedTipoId) ? 'selected' : '';
                        tipoSelect.append(`<option value="${value.id}" ${selected}>${value.nombre}</option>`);
                    });
                    tipoSelect.trigger('change');

                    // si había un tipo seleccionado en edición, cargar sub-tipos
                    if(initialSelectedTipoId){
                        cargarSubTipos(initialSelectedTipoId);
                    }
                },
                error: function (xhr, status, error) {
                console.error("Error al cargar tipos:", xhr.responseText); // 🔍 depuración completa
                tipoSelect.prop('disabled', false).empty().append('<option value="">Error al cargar tipos</option>');
            }
            });
        } else {
            tipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un tipo</option>');
        }
    }

    function cargarSubTipos(tipoId) {
        let subTipoSelect = $('#id_scp_sub_tipo');
        subTipoSelect.prop('disabled', true).empty().append('<option value="">Cargando...</option>');

        if (tipoId) {
            $.ajax({
                url: "{{ route('soportes.subtipos.byTipo', ':tipo') }}".replace(':tipo', tipoId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    subTipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un sub-tipo</option>');
                    $.each(data, function(key, value) {
                        let selected = (value.id == initialSelectedSubTipoId) ? 'selected' : '';
                        subTipoSelect.append(`<option value="${value.id}" ${selected}>${value.nombre}</option>`);
                    });
                    subTipoSelect.trigger('change');
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar sub-tipos:", error);
                    subTipoSelect.prop('disabled', false).empty().append('<option value="">Error al cargar sub-tipos</option>');
                }
            });
        } else {
            subTipoSelect.prop('disabled', false).empty().append('<option value="">Selecciona un sub-tipo</option>');
        }
    }

    function toggleLineaCredito(subTipoId) {
        if (subTipoId == "4") {
            $('#linea_credito_wrapper').show();
        } else {
            $('#linea_credito_wrapper').hide();
            $('#id_cre_lineas_creditos').val('').trigger('change');
        }
    }

    // Eventos
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

    // Cargar tipos si ya hay categoría seleccionada (edición)
    if ($('#id_categoria').val()) {
        cargarTipos($('#id_categoria').val());
    }

    // Si estamos editando, mostrar línea de crédito si corresponde
    if (initialSelectedSubTipoId) {
        toggleLineaCredito(initialSelectedSubTipoId);
    }
});
</script>
@endpush
