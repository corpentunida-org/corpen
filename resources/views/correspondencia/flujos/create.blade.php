<x-base-layout>
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .select2-container--bootstrap-5 .select2-selection { border-radius: 0.5rem; padding: 0.5rem; height: auto; border-color: #dee2e6; }
        .form-control-readonly { background-color: #f8f9fa !important; border-style: dashed; color: #495057; font-weight: 500; }
    </style>
    @endpush

    <div class="app-container">
        <header class="main-header mb-4">
            <h1 class="page-title h3 fw-bold">Crear Flujo de Trabajo</h1>
            <p class="page-subtitle text-muted">El sistema asignará al jefe del área de forma automática.</p>
        </header>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 750px; border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.flujos.store') }}" method="POST" id="form-flujo">
                    @csrf
                    
                    {{-- Nombre del Flujo --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nombre del Flujo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               placeholder="Ej: Aprobación de Facturas" value="{{ old('nombre') }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        {{-- Selección de Área --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Área / Dependencia <span class="text-danger">*</span></label>
                            <select name="id_area" id="id_area" class="form-select" required>
                                <option value="">Buscar área...</option>
                                @foreach($areas as $area)
                                    @php
                                        // Accedemos a la relación 'user' (singular) definida en GdoCargo
                                        $usuarioJefe = $area->jefeCargo->user ?? null;
                                        
                                        $jefeId = $usuarioJefe ? $usuarioJefe->id : '';
                                        $jefeNombre = $usuarioJefe ? $usuarioJefe->name : 'SIN USUARIO VINCULADO';
                                    @endphp
                                    <option value="{{ $area->id }}" 
                                            data-jefe-id="{{ $jefeId }}"
                                            data-jefe-nombre="{{ $jefeNombre }}"
                                            {{ old('id_area') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_area') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        {{-- Responsable (Solo Lectura) --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Responsable (Jefe)</label>
                            <input type="text" id="display_jefe_nombre" class="form-control form-control-readonly" 
                                   placeholder="Seleccione área..." readonly>
                            
                            {{-- ID real que se enviará al controlador --}}
                            <input type="hidden" name="usuario_id" id="usuario_id" value="{{ old('usuario_id') }}">
                            
                            <div id="error-jefe" class="text-danger small mt-1 d-none">
                                <i class="bi bi-x-circle"></i> No se puede crear: El área no tiene un usuario de sistema asignado.
                            </div>
                        </div>
                    </div>

                    {{-- Detalles --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Detalles del Proceso</label>
                        <textarea name="detalle" class="form-control" rows="4">{{ old('detalle') }}</textarea>
                    </div>

                    <div class="d-flex gap-2 pt-3 border-top mt-4">
                        <button type="submit" id="btn-save" class="btn btn-primary px-4 fw-bold shadow-sm" style="background-color: #4f46e5; border:none;">
                            <i class="bi bi-save me-2"></i>Guardar Flujo
                        </button>
                        <a href="{{ route('correspondencia.flujos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            const areaSelect = $('#id_area').select2({ theme: 'bootstrap-5', width: '100%' });

            areaSelect.on('change', function() {
                const selected = $(this).find(':selected');
                const id = selected.data('jefe-id');
                const nombre = selected.data('jefe-nombre');

                if (id) {
                    $('#usuario_id').val(id);
                    $('#display_jefe_nombre').val(nombre).removeClass('is-invalid').addClass('is-valid');
                    $('#error-jefe').addClass('d-none');
                    $('#btn-save').prop('disabled', false);
                } else {
                    $('#usuario_id').val('');
                    $('#display_jefe_nombre').val(nombre).addClass('is-invalid').removeClass('is-valid');
                    
                    if($(this).val() !== "") {
                        $('#error-jefe').removeClass('d-none');
                        $('#btn-save').prop('disabled', true);
                    }
                }
            });

            if (areaSelect.val()) areaSelect.trigger('change');
        });
    </script>
    @endpush
</x-base-layout>