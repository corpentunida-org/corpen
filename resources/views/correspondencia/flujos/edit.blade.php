<x-base-layout>
    {{-- Estilos de Select2 con tema Bootstrap 5 --}}
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.5rem;
            padding: 0.45rem;
            height: auto;
            border: 1px solid #dee2e6;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #212529;
            padding-left: 0.5rem;
            line-height: 1.5;
        }
        .form-control-readonly {
            background-color: #f8f9fa !important;
            border-style: dashed;
            color: #495057;
            font-weight: 500;
        }
    </style>
    @endpush

    <div class="app-container">
        <header class="main-header mb-4">
            <div class="header-content">
                <h1 class="page-title h3 fw-bold">Editar Flujo de Trabajo</h1>
                <p class="page-subtitle text-muted">
                    Modificando el flujo: <span class="badge bg-primary text-white">{{ $flujo->nombre }}</span>
                </p>
            </div>
        </header>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 750px; border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.flujos.update', $flujo) }}" method="POST" id="form-flujo-edit">
                    @csrf 
                    @method('PUT')
                    
                    {{-- Nombre del Flujo --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Flujo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $flujo->nombre) }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        {{-- Selección de Área --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Área / Dependencia <span class="text-danger">*</span></label>
                            <select name="id_area" id="id_area_edit" class="form-select" required>
                                <option value="">Buscar área...</option>
                                @foreach($areas as $area)
                                    @php
                                        // Usamos la relación 'user' definida en tu modelo GdoCargo
                                        $usuarioJefe = $area->jefeCargo->user ?? null;
                                        $jefeId = $usuarioJefe ? $usuarioJefe->id : '';
                                        $jefeNombre = $usuarioJefe ? $usuarioJefe->name : 'SIN JEFE VINCULADO';
                                    @endphp
                                    <option value="{{ $area->id }}" 
                                            data-jefe-id="{{ $jefeId }}"
                                            data-jefe-nombre="{{ $jefeNombre }}"
                                            {{ old('id_area', $flujo->id_area) == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_area') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Responsable Automático (Solo Lectura) --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Responsable Principal</label>
                            <input type="text" id="display_jefe_nombre" class="form-control form-control-readonly" 
                                   value="{{ $flujo->usuario->name ?? 'No asignado' }}" readonly>
                            
                            {{-- ID oculto para el controlador --}}
                            <input type="hidden" name="usuario_id" id="usuario_id_edit" value="{{ old('usuario_id', $flujo->usuario_id) }}">
                            
                            <div id="error-jefe-msg" class="text-danger small mt-1 d-none">
                                <i class="bi bi-exclamation-triangle"></i> No hay un usuario jefe para esta área.
                            </div>
                            @error('usuario_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Detalles --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Detalles del Proceso</label>
                        <textarea name="detalle" class="form-control @error('detalle') is-invalid @enderror" 
                                  rows="4" placeholder="Describa el propósito...">{{ old('detalle', $flujo->detalle) }}</textarea>
                        @error('detalle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Botones de acción --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('correspondencia.flujos.index') }}" class="btn btn-light border px-4">
                            <i class="bi bi-chevron-left me-2"></i>Volver
                        </a>
                        <button type="submit" id="btn-submit-edit" class="btn btn-primary px-5 fw-bold shadow-sm" style="background-color: #4f46e5; border:none;">
                            <i class="bi bi-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts de Select2 --}}
    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2 para Áreas
            const areaSelect = $('#id_area_edit').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Seleccione un área...'
            });

            // Lógica de detección y asignación de jefe
            areaSelect.on('change', function() {
                const selected = $(this).find(':selected');
                const jefeId = selected.data('jefe-id');
                const jefeNombre = selected.data('jefe-nombre');

                if (jefeId) {
                    $('#usuario_id_edit').val(jefeId);
                    $('#display_jefe_nombre').val(jefeNombre).addClass('is-valid').removeClass('is-invalid');
                    $('#error-jefe-msg').addClass('d-none');
                    $('#btn-submit-edit').prop('disabled', false);
                } else {
                    $('#usuario_id_edit').val('');
                    $('#display_jefe_nombre').val(jefeNombre || '').removeClass('is-valid');
                    
                    if($(this).val() !== "") {
                        $('#display_jefe_nombre').addClass('is-invalid');
                        $('#error-jefe-msg').removeClass('d-none');
                        $('#btn-submit-edit').prop('disabled', true);
                    }
                }
            });

            // Disparar validación inicial al cargar (para mostrar estado actual)
            if(areaSelect.val()) {
                // Solo disparamos si no hay errores de validación previos para no pisar el 'old'
                const initialJefeId = areaSelect.find(':selected').data('jefe-id');
                if(!initialJefeId && areaSelect.val() !== "") {
                    $('#btn-submit-edit').prop('disabled', true);
                    $('#error-jefe-msg').removeClass('d-none');
                    $('#display_jefe_nombre').addClass('is-invalid');
                }
            }
        });
    </script>
    @endpush
</x-base-layout>