<x-base-layout>
    {{-- Enlaces cargados directamente para asegurar que funcionen --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    {{-- Estilos insertados directamente (SIN @push) --}}
    <style>
        :root {
            --brand-color: #0284c7;
            --brand-hover: #0369a1;
            --brand-surface: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-subtle: #f1f5f9;
            --border-main: #e2e8f0;
            --focus-ring: rgba(2, 132, 199, 0.15);
        }

        /* Contenedor Principal */
        .card-ux {
            background: var(--brand-surface) !important;
            border: 1px solid var(--border-main) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025) !important;
            padding: 2.5rem !important;
        }

        /* Etiquetas */
        .form-label-ux { 
            font-weight: 600 !important; 
            color: var(--text-main) !important; 
            font-size: 0.875rem !important; 
            margin-bottom: 0.5rem !important; 
            display: block !important; 
        }
        .form-label-ux .required { color: #ef4444; margin-left: 2px; }

        /* Grupos de Input Premium */
        .input-group-ux {
            display: flex !important; 
            align-items: center !important; 
            background: #fff !important;
            border: 1px solid var(--border-main) !important; 
            border-radius: 8px !important;
            transition: all 0.2s ease !important; 
            overflow: hidden !important; 
            position: relative !important;
        }
        .input-group-ux:focus-within {
            border-color: var(--brand-color) !important;
            box-shadow: 0 0 0 4px var(--focus-ring) !important;
        }
        .input-group-ux .icon-wrapper {
            padding: 0.7rem 1rem !important; 
            color: var(--text-muted) !important; 
            background: #f8fafc !important;
            border-right: 1px solid var(--border-main) !important; 
            display: flex !important; 
            align-items: center !important; 
            justify-content: center !important;
        }
        .input-group-ux input, .input-group-ux textarea {
            border: none !important; 
            box-shadow: none !important; 
            padding: 0.7rem 1rem !important; 
            width: 100% !important;
            background: transparent !important; 
            outline: none !important; 
            color: var(--text-main) !important;
        }

        /* Estado ReadOnly Elegante */
        .input-group-ux.readonly-mode {
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            border-style: dashed !important;
        }
        .input-group-ux.readonly-mode input {
            color: #475569 !important; 
            font-weight: 500 !important; 
            cursor: not-allowed !important;
        }

        /* MAGIA CSS: Forzar Select2 a usar nuestro diseño */
        .input-group-ux .select2-container--bootstrap-5 .select2-selection {
            border: none !important;
            background-color: transparent !important;
            box-shadow: none !important;
            min-height: 44px !important;
            padding: 0.35rem 0.5rem !important;
        }
        .input-group-ux .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--text-main) !important;
            font-weight: 400 !important;
        }

        /* Botones Acción */
        .btn-action-primary { 
            background-color: var(--brand-color) !important; 
            color: white !important; 
            border: none !important; 
            padding: 0.6rem 1.5rem !important; 
            border-radius: 8px !important; 
            font-weight: 600 !important; 
            font-size: 0.95rem !important;
            display: inline-flex !important; 
            align-items: center !important; 
            justify-content: center !important;
            transition: all 0.2s ease !important; 
            box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.2) !important; 
            text-decoration: none !important;
        }
        .btn-action-primary:hover { 
            background-color: var(--brand-hover) !important; 
            color: white !important; 
            transform: translateY(-1px) !important; 
        }
        .btn-action-primary:active { 
            transform: scale(0.97) !important; 
            box-shadow: none !important; 
        }

        .btn-outline-ux {
            background-color: #fff !important; 
            color: var(--text-muted) !important; 
            border: 1px solid var(--border-main) !important;
            padding: 0.6rem 1.5rem !important; 
            border-radius: 8px !important; 
            font-weight: 600 !important; 
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            text-decoration: none !important;
        }
        .btn-outline-ux:hover { 
            background-color: #f1f5f9 !important; 
            color: var(--text-main) !important; 
        }
    </style>

    <div class="container-fluid py-5 px-lg-5 max-w-7xl mx-auto">
        
        <!-- Header -->
        <div class="mb-5 text-center">
            <h2 class="fw-bold text-dark mb-2">Editar Flujo de Trabajo</h2>
            <p class="text-muted d-inline-flex align-items-center gap-2">
                Modificando configuración de: 
                <span class="badge" style="background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; border-radius: 50rem; padding: 0.35rem 0.8rem; font-weight: bold; font-size: 0.85rem;">
                    {{ $flujo->nombre }}
                </span>
            </p>
        </div>

        <!-- Tarjeta del Formulario -->
        <div class="card-ux mx-auto" style="max-width: 750px;">
            <form action="{{ route('correspondencia.flujos.update', $flujo) }}" method="POST" id="form-flujo-edit">
                @csrf 
                @method('PUT')
                
                <div class="row g-4">
                    {{-- Nombre del Flujo --}}
                    <div class="col-12 mb-3">
                        <label class="form-label-ux">Nombre del Flujo <span class="required">*</span></label>
                        <div class="input-group-ux @error('nombre') border-danger @enderror">
                            <div class="icon-wrapper"><i class="bi bi-tag text-brand" style="color: #0284c7;"></i></div>
                            <input type="text" name="nombre" value="{{ old('nombre', $flujo->nombre) }}" placeholder="Ej: Flujo de Contratación" required>
                        </div>
                        @error('nombre') <div class="text-danger small mt-1"><i class="bi bi-info-circle me-1"></i>{{ $message }}</div> @enderror
                    </div>

                    {{-- Selección de Área (Con Select2) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label-ux">Área / Dependencia <span class="required">*</span></label>
                        <div class="input-group-ux @error('id_area') border-danger @enderror">
                            <div class="icon-wrapper"><i class="bi bi-building text-brand" style="color: #0284c7;"></i></div>
                            <select name="id_area" id="id_area_edit" required style="width: 100%;">
                                <option value="">Buscar área...</option>
                                @foreach($areas as $area)
                                    @php
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
                        </div>
                        @error('id_area') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Responsable Principal (Solo Lectura) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label-ux">Responsable Principal</label>
                        <div class="input-group-ux readonly-mode" id="jefe_wrapper">
                            <div class="icon-wrapper border-0" style="background: transparent;"><i class="bi bi-lock-fill text-muted opacity-50"></i></div>
                            <input type="text" id="display_jefe_nombre" value="{{ $flujo->usuario->name ?? 'No asignado' }}" readonly>
                        </div>
                        
                        <input type="hidden" name="usuario_id" id="usuario_id_edit" value="{{ old('usuario_id', $flujo->usuario_id) }}">
                        
                        <div id="error-jefe-msg" class="text-danger small mt-2 d-none fw-medium">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Esta área no tiene un jefe vinculado.
                        </div>
                        @error('usuario_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Detalles del Proceso --}}
                    <div class="col-12 mb-4">
                        <label class="form-label-ux">Detalles / Descripción Adicional</label>
                        <div class="input-group-ux" style="align-items: flex-start !important;">
                            <div class="icon-wrapper" style="height: auto; border-right: none; background: transparent; padding-top: 1rem !important;">
                                <i class="bi bi-card-text text-muted"></i>
                            </div>
                            <textarea name="detalle" rows="4" placeholder="Describa el propósito de este flujo..." style="resize: none; padding-left: 0;">{{ old('detalle', $flujo->detalle) }}</textarea>
                        </div>
                        @error('detalle') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Botonera --}}
                    <div class="col-12 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('correspondencia.flujos.show', $flujo) }}" class="btn-outline-ux">
                                <i class="bi bi-arrow-left me-2"></i> Volver
                            </a>
                            <button type="submit" id="btn-submit-edit" class="btn-action-primary">
                                Guardar Cambios <i class="bi bi-check2-circle ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts directos (Sin @push) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            const areaSelect = $('#id_area_edit').select2({
                theme: 'bootstrap-5',
                placeholder: 'Seleccione un área...',
                width: '100%',
                dropdownParent: $('#id_area_edit').parent()
            });

            // Lógica de validación de Jefe al cambiar el Área
            areaSelect.on('change', function() {
                const selected = $(this).find(':selected');
                const jefeId = selected.data('jefe-id');
                const jefeNombre = selected.data('jefe-nombre');
                
                const jefeWrapper = $('#jefe_wrapper');
                const btnSubmit = $('#btn-submit-edit');

                if (jefeId) {
                    $('#usuario_id_edit').val(jefeId);
                    $('#display_jefe_nombre').val(jefeNombre);
                    
                    jefeWrapper.css('border-color', '#10b981').removeClass('border-danger');
                    $('#error-jefe-msg').addClass('d-none');
                    btnSubmit.prop('disabled', false);
                } else {
                    $('#usuario_id_edit').val('');
                    $('#display_jefe_nombre').val(jefeNombre || 'Área sin asignar');
                    
                    if($(this).val() !== "") {
                        jefeWrapper.css('border-color', '#ef4444').addClass('border-danger');
                        $('#error-jefe-msg').removeClass('d-none');
                        btnSubmit.prop('disabled', true);
                    }
                }
            });

            if(areaSelect.val()) {
                const initialJefeId = areaSelect.find(':selected').data('jefe-id');
                if(!initialJefeId && areaSelect.val() !== "") {
                    $('#btn-submit-edit').prop('disabled', true);
                    $('#error-jefe-msg').removeClass('d-none');
                    $('#jefe_wrapper').css('border-color', '#ef4444');
                }
            }
        });
    </script>
</x-base-layout>