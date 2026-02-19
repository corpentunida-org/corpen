<x-base-layout>
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Estilos Pasteles y Minimalistas */
        .bg-pastel-primary { background-color: #e0e7ff; }
        .text-pastel-primary { color: #4338ca; }
        .btn-pastel-primary { background-color: #e0e7ff; color: #4338ca; font-weight: 500; border: none; transition: all 0.2s ease; }
        .btn-pastel-primary:hover { background-color: #c7d2fe; color: #3730a3; transform: translateY(-1px); }
        .btn-pastel-success { background-color: #dcfce7; color: #166534; font-weight: 500; border: none; transition: all 0.2s ease; }
        .btn-pastel-success:hover { background-color: #bbf7d0; color: #14532d; transform: translateY(-1px); }
        
        .form-control, .form-select { border-radius: 10px; border-color: #e2e8f0; padding: 0.6rem 1rem; }
        .form-control:focus, .form-select:focus { border-color: #818cf8; box-shadow: 0 0 0 0.25rem rgba(129, 140, 248, 0.15); }
        
        .select2-container--bootstrap-5 .select2-selection { border-radius: 10px; padding: 0.3rem 0.5rem; border-color: #e2e8f0; }
        .form-control-readonly { background-color: #f8fafc !important; border-style: dashed; border-color: #cbd5e1; color: #475569; font-weight: 500; }
        
        /* Animación de carga */
        .spinner-soft { width: 1.2rem; height: 1.2rem; border-width: 0.15em; }
    </style>
    @endpush

    <div class="app-container py-5 px-4" style="background-color: #fcfcfd; min-height: 100vh;">
        <header class="main-header mb-4 text-center">
            <h1 class="page-title h3 fw-bold text-dark mb-1">Crear Flujo de Trabajo</h1>
            <p class="page-subtitle text-muted" style="font-size: 0.9rem;">El sistema asignará al jefe del área de forma automática.</p>
        </header>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 750px; border-radius: 20px; background: #ffffff;">
            <div class="card-body p-5">
                {{-- Formulario Principal: Flujo --}}
                <form action="{{ route('correspondencia.flujos.store') }}" method="POST" id="form-flujo">
                    @csrf
                    
                    {{-- Nombre del Flujo --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Nombre del Flujo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control form-control-lg bg-light border-0 @error('nombre') is-invalid @enderror" 
                               placeholder="Ej: Aprobación de Facturas" value="{{ old('nombre') }}" required>
                        <div class="invalid-feedback" id="error-nombre"></div>
                    </div>

                    <div class="row g-4 mb-4">
                        {{-- Selección de Área --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Área / Dependencia <span class="text-danger">*</span></label>
                            <select name="id_area" id="id_area" class="form-select bg-light border-0" required>
                                <option value="">Buscar área...</option>
                                @foreach($areas as $area)
                                    @php
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
                            <div class="invalid-feedback" id="error-id_area"></div>
                        </div>

                        {{-- Responsable (Solo Lectura) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Responsable (Jefe)</label>
                            <input type="text" id="display_jefe_nombre" class="form-control form-control-readonly" 
                                   placeholder="Seleccione un área primero..." readonly>
                            
                            {{-- ID real que se enviará al controlador --}}
                            <input type="hidden" name="usuario_id" id="usuario_id" value="{{ old('usuario_id') }}">
                            
                            <div id="error-jefe" class="text-danger small mt-2 d-none fw-medium">
                                <i class="fas fa-exclamation-circle me-1"></i> El área no tiene un usuario asignado.
                            </div>
                        </div>
                    </div>

                    {{-- Detalles --}}
                    <div class="mb-5">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.5px;">Detalles del Proceso</label>
                        <textarea name="detalle" class="form-control bg-light border-0" rows="3" placeholder="Opcional: Describa el propósito general de este flujo...">{{ old('detalle') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <a href="{{ route('correspondencia.flujos.index') }}" class="btn btn-light rounded-pill px-4 text-muted fw-medium border-0">Cancelar</a>
                        <button type="submit" id="btn-save" class="btn btn-pastel-primary rounded-pill px-5 shadow-sm">
                            Guardar y Configurar TRD <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL TRD (Se abre automáticamente por JS) --}}
    <div class="modal fade" id="modalTRD" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-pastel-primary border-0 pt-4 px-5 rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-2 rounded-circle me-3 shadow-sm text-pastel-primary d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-folder-open fs-5"></i>
                        </div>
                        <div>
                            <h4 class="modal-title fw-bold text-pastel-primary m-0">Serie Documental (TRD)</h4>
                            <p class="small text-muted m-0">Configure la retención para el nuevo flujo.</p>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('correspondencia.trds.store') }}" method="POST" id="form-trd">
                    @csrf
                    <div class="modal-body p-5">
                        <input type="hidden" name="usuario_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="fk_flujo" id="modal_fk_flujo" value="">

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Flujo Vinculado</label>
                            <input type="text" id="modal_flujo_nombre" class="form-control form-control-readonly text-pastel-primary fw-bold border-0" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary small text-uppercase">Nombre de la Serie Documental <span class="text-danger">*</span></label>
                            <input type="text" name="serie_documental" class="form-control form-control-lg bg-light border-0" 
                                   placeholder="Ej: Historias Laborales" required>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small text-uppercase">Archivo Gestión (Años) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                    <span class="input-group-text bg-white border-0"><i class="fas fa-briefcase text-info"></i></span>
                                    <input type="number" name="tiempo_gestion" class="form-control border-0" value="0" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small text-uppercase">Archivo Central (Años) <span class="text-danger">*</span></label>
                                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                    <span class="input-group-text bg-white border-0"><i class="fas fa-building text-warning"></i></span>
                                    <input type="number" name="tiempo_central" class="form-control border-0" value="0" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-4 bg-light">
                            <label class="form-label fw-semibold text-secondary small text-uppercase d-block mb-3">Disposición Final <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="disposicion_final" id="disp1" value="conservar" checked>
                                    <label class="form-check-label fw-medium text-success" for="disp1">
                                        <i class="fas fa-archive me-1"></i> Conservación Total
                                    </label>
                                </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="disposicion_final" id="disp2" value="eliminar">
                                    <label class="form-check-label fw-medium text-danger" for="disp2">
                                        <i class="fas fa-trash-alt me-1"></i> Eliminación
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pb-5 px-5 d-flex justify-content-between">
                        {{-- Modificado para que se actualice dinámicamente con JS --}}
                        <a href="#" id="btn-omitir" class="btn btn-light rounded-pill px-4 fw-medium border-0">Omitir por ahora</a>
                        <button type="submit" id="btn-save-trd" class="btn btn-pastel-success rounded-pill px-5 shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> Finalizar y Guardar
                        </button>
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

            // Lógica para auto-completar el Jefe
            areaSelect.on('change', function() {
                const selected = $(this).find(':selected');
                const id = selected.data('jefe-id');
                const nombre = selected.data('jefe-nombre');

                if (id) {
                    $('#usuario_id').val(id);
                    $('#display_jefe_nombre').val(nombre).removeClass('is-invalid').addClass('is-valid border-success');
                    $('#error-jefe').addClass('d-none');
                    $('#btn-save').prop('disabled', false);
                } else {
                    $('#usuario_id').val('');
                    $('#display_jefe_nombre').val(nombre).addClass('is-invalid border-danger').removeClass('is-valid border-success');
                    if($(this).val() !== "") {
                        $('#error-jefe').removeClass('d-none');
                        $('#btn-save').prop('disabled', true);
                    }
                }
            });

            if (areaSelect.val()) areaSelect.trigger('change');

            // 1. LÓGICA AJAX: Enviar Flujo y Abrir Modal
            $('#form-flujo').on('submit', async function(e) {
                e.preventDefault();
                
                const form = this;
                const btn = $('#btn-save');
                const originalHtml = btn.html();
                
                btn.html('<span class="spinner-border spinner-soft me-2"></span> Guardando...');
                btn.prop('disabled', true);
                $('.invalid-feedback').text('');
                $('.form-control, .form-select').removeClass('is-invalid');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            for (const [field, messages] of Object.entries(data.errors)) {
                                $(`[name="${field}"]`).addClass('is-invalid');
                                $(`#error-${field}`).text(messages[0]).show();
                            }
                        } else {
                            alert('Ocurrió un error al guardar. Inténtelo de nuevo.');
                        }
                        btn.html(originalHtml);
                        btn.prop('disabled', false);
                        return;
                    }

                    // Inyectar datos en el Modal TRD
                    $('#modal_fk_flujo').val(data.flujo_id);
                    $('#modal_flujo_nombre').val($('[name="nombre"]').val());
                    
                    // Actualizar el enlace "Omitir" para que vaya al show del Flujo
                    $('#btn-omitir').attr('href', `{{ url('correspondencia/flujos') }}/${data.flujo_id}`);

                    btn.html('<i class="fas fa-check me-2"></i> Flujo Creado');
                    const trdModal = new bootstrap.Modal(document.getElementById('modalTRD'));
                    trdModal.show();

                } catch (error) {
                    alert('Error de conexión.');
                    btn.html(originalHtml);
                    btn.prop('disabled', false);
                }
            });

            // 2. LÓGICA AJAX: Enviar TRD y Redirigir al Show del Flujo
            $('#form-trd').on('submit', async function(e) {
                e.preventDefault();
                
                const form = this;
                const btn = $('#btn-save-trd');
                const originalHtml = btn.html();
                
                btn.html('<span class="spinner-border spinner-soft me-2"></span> Finalizando...');
                btn.prop('disabled', true);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    // Si todo va bien (o si el backend hace un redirect normal, response.ok será true)
                    if (response.ok) {
                        const flujoId = $('#modal_fk_flujo').val();
                        // Redirección forzada hacia el Show del Flujo
                        window.location.href = `{{ url('correspondencia/flujos') }}/${flujoId}`;
                    } else {
                        alert('Ocurrió un error al guardar la Serie Documental.');
                        btn.html(originalHtml);
                        btn.prop('disabled', false);
                    }
                } catch (error) {
                    alert('Error de conexión.');
                    btn.html(originalHtml);
                    btn.prop('disabled', false);
                }
            });
        });
    </script>
    @endpush
</x-base-layout>