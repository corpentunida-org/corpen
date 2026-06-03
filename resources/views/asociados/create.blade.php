<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header mb-4">
            <div class="header-titles">
                <span class="system-tag text-uppercase fw-bold text-primary">ECM - Módulo de Asociados</span>
                <h1 class="main-title">Apertura de Expediente</h1>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
            </div>
        </header>

        {{-- Alerta general de error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-2"></i> Error:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Alerta de validación general --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong><i class="fas fa-exclamation-circle me-2"></i> Por favor corrija los errores marcados en el
                    formulario.</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('asociados.maestro.store') }}" method="POST" id="form-expediente">
            @csrf

            {{-- Input oculto para definir si se sincroniza con MaeTerceros --}}
            <input type="hidden" name="sincronizar_tercero" id="input_sincronizar_tercero" value="1">

            {{-- DATALIST GLOBAL PARA CIUDADES --}}
            <datalist id="ciudades_list">
                @foreach($ciudades as $ciudad)
                    {{-- El value es el texto visible, el data-id es lo que guardaremos --}}
                    <option data-id="{{ $ciudad->id_ciudad }}" value="{{ $ciudad->nombre }}, {{ $ciudad->subregion->nombre ?? 'Sin región' }}"></option>
                @endforeach
            </datalist>

            {{-- DATALIST GLOBAL PARA DISTRITOS --}}
            <datalist id="distritos_list">
                @foreach($distritos as $distrito)
                    <option data-id="{{ $distrito->COD_DIST }}" value="{{ $distrito->NOM_DIST }}"></option>
                @endforeach
            </datalist>

            {{-- DATALIST PARA CONGREGACIONES --}}
            <datalist id="congregaciones_list">
                @foreach($congregaciones as $congre)
                    <option data-id="{{ $congre->codigo }}" value="{{ $congre->nombre }}"></option>
                @endforeach
            </datalist>

            {{-- =========================================================================
            SECCIÓN 1: IDENTIDAD Y DEMOGRÁFICOS (EDICIÓN PREMIUM CORP)
            ========================================================================= --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-light-primary text-primary rounded me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-id-card fs-5"></i>
                        </div>
                        <div>
                            <h5 class="text-dark fw-bold mb-0">1. Identidad y Demográficos</h5>
                            <small class="text-muted">Información personal básica, documento de identidad y estado
                                civil.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="row g-3">
                        {{-- Fila 1: Documento y Estado Civil --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Cédula <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-sm shadow-sm rounded has-validation">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="far fa-id-badge"></i></span>
                                <input type="text" name="cedula" id="cedula"
                                    class="form-control border-start-0 @error('cedula') is-invalid @enderror"
                                    value="{{ old('cedula') }}" required placeholder="No. de documento">
                                <button class="btn btn-primary px-3" type="button" id="btn-buscar-cedula"
                                    style="z-index: 0;">
                                    <i class="fas fa-search" id="icon-buscar"></i>
                                    <i class="fas fa-spinner fa-spin d-none" id="spinner-buscar"></i>
                                </button>
                                @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- LUGAR DE EXPEDICIÓN --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Lugar de Expedición</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-primary border-end-0"><i class="fas fa-map-marker-alt"></i></span>
                                
                                {{-- Campo oculto para el ID real (viaja en el request) --}}
                                <input type="hidden" name="lugar_expedicion_cedula" id="hidden_lugar_expedicion" value="{{ old('lugar_expedicion_cedula') }}">
                                
                                {{-- Input visible (No viaja en el request, solo muestra nombres) --}}
                                <input type="text" list="ciudades_list" id="input_lugar_expedicion"
                                    class="form-control border-start-0 @error('lugar_expedicion_cedula') is-invalid @enderror"
                                    placeholder="Escriba para buscar...">
                                
                                @error('lugar_expedicion_cedula') 
                                    <div class="invalid-feedback d-block">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Fecha de Expedición</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="far fa-calendar-check"></i></span>
                                <input type="date" name="fecha_expedicion" id="fecha_expedicion"
                                    class="form-control border-start-0 @error('fecha_expedicion') is-invalid @enderror"
                                    value="{{ old('fecha_expedicion') }}">
                                @error('fecha_expedicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Estado Civil</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-ring"></i></span>
                                <select name="estado_civil" id="estado_civil"
                                    class="form-select border-start-0 @error('estado_civil') is-invalid @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>
                                        Soltero(a)</option>
                                    <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>
                                        Casado(a)</option>
                                    <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo(a)
                                    </option>
                                </select>
                                @error('estado_civil') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Fila 2: Nombres y Apellidos --}}
                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Primer Nombre <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-user"></i></span>
                                <input type="text" name="nombre1" id="nombre1"
                                    class="form-control border-start-0 @error('nombre1') is-invalid @enderror"
                                    value="{{ old('nombre1') }}" required placeholder="Primer nombre">
                                @error('nombre1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Segundo Nombre</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="far fa-user"></i></span>
                                <input type="text" name="nombre2" id="nombre2" class="form-control border-start-0"
                                    value="{{ old('nombre2') }}" placeholder="Segundo nombre (Opcional)">
                            </div>
                        </div>

                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Primer Apellido <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-user-tag"></i></span>
                                <input type="text" name="apellido1" id="apellido1"
                                    class="form-control border-start-0 @error('apellido1') is-invalid @enderror"
                                    value="{{ old('apellido1') }}" required placeholder="Primer apellido">
                                @error('apellido1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Segundo Apellido</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-user-tag" style="opacity: 0.6;"></i></span>
                                <input type="text" name="apellido2" id="apellido2" class="form-control border-start-0"
                                    value="{{ old('apellido2') }}" placeholder="Segundo apellido">
                            </div>
                        </div>

                        {{-- Fila 3: Nacimiento --}}
                        <div class="col-md-3 mt-4 mb-2">
                            <label class="form-label fw-semibold text-secondary small mb-1">Fecha de Nacimiento</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-birthday-cake"></i></span>
                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                    class="form-control border-start-0 @error('fecha_nacimiento') is-invalid @enderror"
                                    value="{{ old('fecha_nacimiento') }}">
                                @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
            SECCIÓN 2: CONTACTO Y NÚCLEO FAMILIAR (EDICIÓN PREMIUM CORP)
            ========================================================================= --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-light-primary text-primary rounded me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-users fs-5"></i>
                        </div>
                        <div>
                            <h5 class="text-dark fw-bold mb-0">2. Contacto y Núcleo Familiar</h5>
                            <small class="text-muted">Parámetros de localización, canales corporativos e información
                                familiar.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    {{-- Subsección: Datos del Pastor --}}
                    <div class="border-bottom pb-2 mb-3">
                        <h6 class="text-secondary fw-bold mb-0" style="font-size: 0.9rem; letter-spacing: 0.3px;">
                            <i class="fas fa-user-tie me-2 text-muted"></i>Datos de Contacto del Pastor
                        </h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Correo Electrónico</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-envelope"></i></span>
                                <input type="email" name="correo_pastor" id="correo_pastor"
                                    class="form-control border-start-0 @error('correo_pastor') is-invalid @enderror"
                                    value="{{ old('correo_pastor') }}" placeholder="ejemplo@dominio.com">
                                @error('correo_pastor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Celular Principal</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-mobile-alt"></i></span>
                                <input type="text" name="celular_pastor" id="celular_pastor"
                                    class="form-control border-start-0" value="{{ old('celular_pastor') }}"
                                    placeholder="300 000 0000">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">WhatsApp</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fab fa-whatsapp fw-bold text-success"></i></span>
                                <input type="text" name="whatsapp" id="whatsapp" class="form-control border-start-0"
                                    value="{{ old('whatsapp') }}" placeholder="300 000 0000">
                            </div>
                        </div>
                    </div>

                    {{-- Subsección: Datos de la Esposa --}}
                    <div class="border-bottom pb-2 mb-3 mt-4">
                        <h6 class="text-secondary fw-bold mb-0" style="font-size: 0.9rem; letter-spacing: 0.3px;">
                            <i class="fas fa-female me-2 text-muted"></i>Datos de la Esposa <span
                                class="fw-normal text-muted small">(Si aplica)</span>
                        </h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Cédula Esposa</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="far fa-id-card"></i></span>
                                <input type="text" name="cedula_esposa" id="cedula_esposa"
                                    class="form-control border-start-0" value="{{ old('cedula_esposa') }}"
                                    placeholder="Número de documento">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Nombre Completo</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-user"></i></span>
                                <input type="text" name="nombre_esposa" id="nombre_esposa"
                                    class="form-control border-start-0" value="{{ old('nombre_esposa') }}"
                                    placeholder="Nombres y apellidos">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Celular Esposa</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-phone"></i></span>
                                <input type="text" name="celular_esposa" id="celular_esposa"
                                    class="form-control border-start-0" value="{{ old('celular_esposa') }}"
                                    placeholder="Número telefónico">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Correo Esposa</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-at"></i></span>
                                <input type="email" name="correo_esposa" id="correo_esposa"
                                    class="form-control border-start-0 @error('correo_esposa') is-invalid @enderror"
                                    value="{{ old('correo_esposa') }}" placeholder="esposa@dominio.com">
                                @error('correo_esposa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
            SECCIÓN 3: INFORMACIÓN MINISTERIAL Y CORPORATIVA
            ========================================================================= --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-light-primary text-primary rounded me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-church fs-5"></i>
                        </div>
                        <div>
                            <h5 class="text-dark fw-bold mb-0">3. Información Corpentunida</h5>
                            <small class="text-muted">Datos de afiliación, distrito eclesiástico y estatus pastoral
                                actual.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="row g-3">
                        {{-- Fila 1: Datos Ministeriales --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Fecha de Afiliación</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="far fa-calendar-alt"></i></span>
                                <input type="date" name="fecha_afiliacion" id="fecha_afiliacion"
                                    class="form-control border-start-0 @error('fecha_afiliacion') is-invalid @enderror"
                                    value="{{ old('fecha_afiliacion') }}">
                                @error('fecha_afiliacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Estado Asociado</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-user-check"></i></span>
                                <select name="estado_pastor" id="estado_pastor"
                                    class="form-select border-start-0 @error('estado_pastor') is-invalid @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="Activo" {{ old('estado_pastor') == 'Activo' ? 'selected' : '' }}>Activo
                                    </option>
                                    <option value="Inactivo" {{ old('estado_pastor') == 'Inactivo' ? 'selected' : '' }}>
                                        Inactivo</option>
                                    <option value="Retirado" {{ old('estado_pastor') == 'Retirado' ? 'selected' : '' }}>
                                        Retirado</option>
                                </select>
                                @error('estado_pastor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Licencia Pastoral</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-certificate"></i></span>
                                <input type="text" name="licencia" id="licencia" class="form-control border-start-0"
                                    value="{{ old('licencia') }}" placeholder="Ej: Ordenado, Licenciado...">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Especificación</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-tag"></i></span>
                                <input type="text" name="especificacion" id="especificacion"
                                    class="form-control border-start-0" value="{{ old('especificacion') }}"
                                    placeholder="Cargo o rol específico">
                            </div>
                        </div>

                        {{-- Fila 2: Ubicación Eclesiástica --}}
                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">País</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-globe-americas"></i></span>
                                <input type="text" name="pais" id="pais" class="form-control border-start-0"
                                    value="{{ old('pais', 'Colombia') }}">
                            </div>
                        </div>

                        {{-- DISTRITO ACTUAL --}}
                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Distrito Actual</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-map-marked-alt"></i></span>
                                
                                <input type="hidden" name="distrito_actual" id="hidden_distrito_actual" value="{{ old('distrito_actual') }}">
                                <input type="text" list="distritos_list" id="input_distrito_actual"
                                    class="form-control border-start-0 @error('distrito_actual') is-invalid @enderror" 
                                    placeholder="Escriba para buscar...">
                                
                                @error('distrito_actual')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- CIUDAD DEL DISTRITO --}}
                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Ciudad del Distrito</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-success border-end-0"><i class="fas fa-city"></i></span>
                                
                                <input type="hidden" name="ciudad_distrito" id="hidden_ciudad_distrito" value="{{ old('ciudad_distrito') }}">
                                <input type="text" list="ciudades_list" id="input_ciudad_distrito"
                                    class="form-control border-start-0 @error('ciudad_distrito') is-invalid @enderror"
                                    placeholder="Escriba para buscar...">
                                    
                                @error('ciudad_distrito')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- IGLESIA / CONGREGACIÓN --}}
                        <div class="col-md-3 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Iglesia / Congregación</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-place-of-worship"></i></span>
                                
                                <input type="hidden" name="iglesia_actual" id="hidden_iglesia_actual" value="{{ old('iglesia_actual') }}">
                                <input type="text" list="congregaciones_list" id="input_iglesia_actual"
                                    class="form-control border-start-0" placeholder="Escriba para buscar...">
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <label class="form-label fw-semibold text-secondary small mb-1">Dirección Eclesiástica /
                                Distrito</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="direccion_distrito" id="direccion_distrito"
                                    class="form-control border-start-0" value="{{ old('direccion_distrito') }}"
                                    placeholder="Dirección completa">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
            SECCIÓN 4: GESTIÓN DE ARCHIVO FÍSICO Y DIGITAL (EDICIÓN PREMIUM CORP)
            ========================================================================= --}}
            <div class="card shadow-sm border-0 mb-4 rounded-3 border-start border-info border-4 bg-light">
                <div class="card-header border-bottom-0 pt-4 pb-2 bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-white text-info shadow-sm rounded me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-file-archive fs-5"></i>
                        </div>
                        <div>
                            <h5 class="text-dark fw-bold mb-0">4. Control de Archivo y ECM</h5>
                            <small class="text-muted">Trazabilidad topográfica del expediente, anexos y estatus de
                                digitalización.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">

                    {{-- Subsección: Soportes Documentales --}}
                    <div class="border-bottom border-secondary border-opacity-25 pb-2 mb-3">
                        <h6 class="text-secondary fw-bold mb-0" style="font-size: 0.9rem; letter-spacing: 0.3px;">
                            <i class="fas fa-folder-plus me-2 text-muted"></i>Soportes Documentales Entregados
                        </h6>
                    </div>

                    <div class="row g-2 mb-4 bg-white p-3 rounded shadow-sm">
                        @php
                            $documentos = [
                                'doc_formulario_afiliacion' => ['label' => 'Form. Afiliación', 'icon' => 'fa-file-signature'],
                                'doc_autorizacion_datos' => ['label' => 'Habeas Data', 'icon' => 'fa-user-shield'],
                                'doc_cedula_pastor' => ['label' => 'CC Pastor', 'icon' => 'fa-id-card'],
                                'doc_cedula_esposa' => ['label' => 'CC Esposa', 'icon' => 'fa-id-card'],
                                'doc_licencia_pastoral' => ['label' => 'Licencia Pastoral', 'icon' => 'fa-certificate'],
                                'doc_registro_matrimonio' => ['label' => 'Reg. Matrimonio', 'icon' => 'fa-ring'],
                                'doc_id_hijos' => ['label' => 'Docs Hijos', 'icon' => 'fa-child']
                            ];
                        @endphp

                        @foreach($documentos as $campo => $data)
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-secondary mb-1" style="font-size:0.8rem;">
                                    <i class="fas {{ $data['icon'] }} text-muted me-1"></i>{{ $data['label'] }}
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i
                                            class="fas fa-tasks"></i></span>
                                    <select name="{{ $campo }}" class="form-select border-start-0 text-dark"
                                        style="font-size: 0.85rem;">
                                        <option value="Pendiente" {{ old($campo) == 'Pendiente' ? 'selected' : '' }}>Pendiente
                                        </option>
                                        <option value="Entregado" {{ old($campo) == 'Entregado' ? 'selected' : '' }}>Entregado
                                        </option>
                                        <option value="No Aplica" {{ old($campo) == 'No Aplica' ? 'selected' : '' }}>No Aplica
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Subsección: Archivo Físico --}}
                    <div class="border-bottom border-secondary border-opacity-25 pb-2 mb-3 mt-4">
                        <h6 class="text-secondary fw-bold mb-0" style="font-size: 0.9rem; letter-spacing: 0.3px;">
                            <i class="fas fa-boxes me-2 text-muted"></i>Localización de Archivo Físico
                        </h6>
                    </div>

                    <div class="row g-3 mb-4 bg-white p-3 rounded shadow-sm">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Nº Radicado</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-barcode"></i></span>
                                <input type="text" name="radicado"
                                    class="form-control border-start-0 font-monospace fw-bold text-primary"
                                    value="{{ old('radicado') }}" placeholder="EXP-000">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Ubicación Carpeta</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-folder-open"></i></span>
                                <input type="text" name="ubicacion_carpeta" class="form-control border-start-0"
                                    value="{{ old('ubicacion_carpeta') }}" placeholder="Ej. Estante A, Fila 2">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-secondary small mb-1">Número de Caja</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-box"></i></span>
                                <input type="text" name="numero_caja" class="form-control border-start-0"
                                    value="{{ old('numero_caja') }}" placeholder="Caja No.">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-secondary small mb-1">Cant. Folios</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-layer-group"></i></span>
                                <input type="number" name="cantidad_folios"
                                    class="form-control border-start-0 @error('cantidad_folios') is-invalid @enderror"
                                    value="{{ old('cantidad_folios', 0) }}" min="0">
                                @error('cantidad_folios') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-secondary small mb-1">Estado Papel</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-file-alt"></i></span>
                                <select name="estado_conservacion" class="form-select border-start-0">
                                    <option value="Bueno" {{ old('estado_conservacion') == 'Bueno' ? 'selected' : '' }}>
                                        Bueno</option>
                                    <option value="Regular" {{ old('estado_conservacion') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Malo" {{ old('estado_conservacion') == 'Malo' ? 'selected' : '' }}>Malo
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 mt-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Fecha Ingreso
                                Archivo</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="far fa-calendar-plus"></i></span>
                                <input type="date" name="fecha_ingreso_archivo" class="form-control border-start-0"
                                    value="{{ old('fecha_ingreso_archivo') }}">
                            </div>
                        </div>

                        <div class="col-md-3 mt-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Custodia Actual</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-building"></i></span>
                                <input type="text" name="custodia_actual" class="form-control border-start-0"
                                    value="{{ old('custodia_actual', 'Archivo Central') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Observaciones Archivo
                                Físico</label>
                            <div class="input-group input-group-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-comment"></i></span>
                                <input type="text" name="observaciones_archivo" class="form-control border-start-0"
                                    value="{{ old('observaciones_archivo') }}"
                                    placeholder="Notas sobre el estado físico de la carpeta...">
                            </div>
                        </div>
                    </div>

                    {{-- Subsección: Archivo Digital (ECM) --}}
                    <div class="border-bottom border-secondary border-opacity-25 pb-2 mb-3 mt-4">
                        <h6 class="text-secondary fw-bold mb-0" style="font-size: 0.9rem; letter-spacing: 0.3px;">
                            <i class="fas fa-cloud me-2 text-muted"></i>Integración ECM (Digital)
                        </h6>
                    </div>

                    <div class="row g-3 bg-white p-3 rounded shadow-sm">
                        <div class="col-md-12 mb-3">
                            <div class="d-flex flex-wrap gap-4 bg-light p-3 rounded border">
                                <div class="form-check form-switch">
                                    <input class="form-check-input shadow-sm" type="checkbox" role="switch"
                                        name="escaneado" id="es1" value="1" {{ old('escaneado') ? 'checked' : '' }}>
                                    <label class="form-check-label text-secondary fw-bold small" for="es1"><i
                                            class="fas fa-print me-1"></i> Escaneado</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input shadow-sm" type="checkbox" role="switch"
                                        name="cargado_ecm" id="ec1" value="1" {{ old('cargado_ecm') ? 'checked' : '' }}>
                                    <label class="form-check-label text-primary fw-bold small" for="ec1"><i
                                            class="fas fa-cloud-upload-alt me-1"></i> Cargado en Plataforma ECM</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input shadow-sm" type="checkbox" role="switch"
                                        name="validado_archivo" id="va1" value="1" {{ old('validado_archivo') ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-bold small" for="va1"><i
                                            class="fas fa-check-double me-1"></i> Expediente Validado</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-secondary small mb-1">Enlace / URL de Plataforma
                                ECM</label>
                            <div class="input-group input-group-sm rounded shadow-sm">
                                <span class="input-group-text bg-light text-info border-end-0"><i
                                        class="fas fa-link"></i></span>
                                <input type="url" name="ubicacion_ecm_link"
                                    class="form-control border-start-0 text-info @error('ubicacion_ecm_link') is-invalid @enderror"
                                    value="{{ old('ubicacion_ecm_link') }}"
                                    placeholder="https://ruta-del-servidor.com/expediente-digital">
                                @error('ubicacion_ecm_link') <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
            SECCIÓN 5: RESULTADOS Y CIERRE (ESTADO DEL EXPEDIENTE Y OBSERVACIONES FINALES)
            ========================================================================= --}}
            <div class="card shadow-sm border-0 mb-5 rounded-3 border-start border-secondary border-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-light text-secondary border rounded me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="fas fa-tags fs-5"></i>
                        </div>
                        <div>
                            <h5 class="text-dark fw-bold mb-0">5. Resultados y Cierre</h5>
                            <small class="text-muted">Estado del expediente e información adicional de
                                observación.</small>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Estado del
                                Expediente</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-toggle-on"></i></span>
                                <select name="estado" class="form-select border-start-0 fw-bold text-dark">
                                    <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>ACTIVO
                                    </option>
                                    <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>INACTIVO
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <label class="form-label fw-semibold text-secondary small mb-1">Observaciones
                                Generales</label>
                            <div class="input-group input-group-sm shadow-sm rounded">
                                <span class="input-group-text bg-light text-muted border-end-0"><i
                                        class="fas fa-comment-dots"></i></span>
                                <textarea name="observaciones_generales" class="form-control border-start-0" rows="2"
                                    placeholder="Cualquier nota adicional sobre la apertura o estado de este expediente...">{{ old('observaciones_generales') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =========================================================================
            BOTÓN DE ENVÍO (STICKY BOTTOM)
            ========================================================================= --}}
            <div class="sticky-bottom bg-white px-4 py-3 border-top shadow-lg d-flex justify-content-between align-items-center"
                style="bottom: 0; z-index: 1000; margin-left: -1rem; margin-right: -1rem;">
                <div class="d-none d-md-block">
                    <span class="text-muted small fw-semibold">
                        <i class="fas fa-shield-alt text-success me-1"></i> Verifique que los campos obligatorios (*)
                        estén completos.
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('asociados.maestro.index') }}"
                        class="btn btn-light border px-4 py-2 fw-semibold text-secondary">
                        Cancelar
                    </a>
                    <button type="submit" id="btn-submit"
                        class="btn btn-dark px-5 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center"
                        style="min-width: 250px; border-radius: 6px;">
                        <span id="btn-text"><i class="fas fa-save me-2 text-info"></i> Consolidar Expediente</span>
                        <span id="btn-spinner" class="d-none"><i class="fas fa-circle-notch fa-spin me-2 text-info"></i>
                            Procesando...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ==========================================
    MODAL DE CONFIRMACIÓN DE SINCRONIZACIÓN
    ========================================== --}}
    <div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="syncModalLabel"><i class="fas fa-sync-alt me-2"></i> Sincronización de
                        Datos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Desea actualizar/sincronizar la información de este asociado con la <strong>Base de Datos
                            Maestra de Terceros (MaeTerceros)</strong>?</p>
                    <p class="text-muted small"><i class="fas fa-info-circle"></i> Si selecciona "Sí", los datos
                        personales y de contacto sobrescribirán el registro actual en el directorio global.</p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" id="btn-deny-sync">
                        <i class="fas fa-times me-1"></i> No, guardar solo aquí
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-confirm-sync">
                        <i class="fas fa-check me-1"></i> Sí, sincronizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // =====================================================================
            // LÓGICA DE NEGOCIO (Vainilla JS)
            // =====================================================================
            document.addEventListener('DOMContentLoaded', function () {
                const inputs = [
                    { vis: 'input_lugar_expedicion', hid: 'hidden_lugar_expedicion', list: 'ciudades_list' },
                    { vis: 'input_distrito_actual', hid: 'hidden_distrito_actual', list: 'distritos_list' },
                    { vis: 'input_ciudad_distrito', hid: 'hidden_ciudad_distrito', list: 'ciudades_list' },
                    { vis: 'input_iglesia_actual', hid: 'hidden_iglesia_actual', list: 'congregaciones_list' }
                ];

                // Función auxiliar para auto-completar inputs visuales cuando el hidden tiene un ID (ej: after validation fail / AJAX)
                function setDatalistValue(hiddenId, visibleId, listId, valueToSet) {
                    const hiddenEl = document.getElementById(hiddenId);
                    const visibleEl = document.getElementById(visibleId);
                    const dataList = document.getElementById(listId);

                    hiddenEl.value = valueToSet;
                    
                    if (valueToSet && dataList) {
                        // Buscar la opción cuyo data-id sea el valor enviado
                        const option = Array.from(dataList.options).find(opt => opt.getAttribute('data-id') == valueToSet);
                        visibleEl.value = option ? option.value : valueToSet; // Si existe muestra el nombre, sino muestra lo que hay
                    } else {
                        visibleEl.value = '';
                    }
                }

                inputs.forEach(item => {
                    const inputVisible = document.getElementById(item.vis);
                    const inputHidden = document.getElementById(item.hid);
                    const dataList = document.getElementById(item.list);

                    // 1. Carga inicial (Por si viene con "old()" tras un error de validación)
                    if (inputHidden.value) {
                        setDatalistValue(item.hid, item.vis, item.list, inputHidden.value);
                    }

                    // 2. Evento para cuando el usuario escribe en el campo visual
                    inputVisible.addEventListener('input', function() {
                        const val = this.value;
                        // Busca la opción que tenga como VALUE exactamente el texto ingresado
                        const option = Array.from(dataList.options).find(opt => opt.value === val);
                        
                        if (option) {
                            inputHidden.value = option.getAttribute('data-id'); // Guarda el ID real
                        } else {
                            inputHidden.value = ''; // Limpia si no coincide
                        }
                    });
                });

                // 1. LÓGICA DE BÚSQUEDA DE CÉDULA VIA AJAX
                const btnBuscar = document.getElementById('btn-buscar-cedula');
                const inputCedula = document.getElementById('cedula');

                // Función centralizada para limpiar los campos
                function limpiarCamposFormulario() {
                    const camposALimpiar = [
                        'nombre1', 'nombre2', 'apellido1', 'apellido2',
                        'fecha_nacimiento', 'fecha_expedicion', 'hidden_lugar_expedicion', 'input_lugar_expedicion',
                        'estado_civil', 'correo_pastor', 'celular_pastor', 'whatsapp',
                        'hidden_distrito_actual', 'input_distrito_actual', 'hidden_ciudad_distrito', 'input_ciudad_distrito', 
                        'hidden_iglesia_actual', 'input_iglesia_actual', 'fecha_afiliacion',
                        'cedula_esposa', 'nombre_esposa', 'correo_esposa', 'celular_esposa'
                    ];

                    camposALimpiar.forEach(id => {
                        const elemento = document.getElementById(id);
                        if (elemento) elemento.value = '';
                    });

                    // Restaurar valores por defecto específicos
                    const pais = document.getElementById('pais');
                    if (pais) pais.value = 'Colombia';

                    // Remover feedback visual
                    inputCedula.classList.remove('is-valid');
                }

                // Evento: Limpiar en tiempo real si el usuario borra todo
                inputCedula.addEventListener('input', function () {
                    if (this.value.trim() === '') {
                        limpiarCamposFormulario();
                    }
                });

                btnBuscar.addEventListener('click', async function () {
                    const cedula = inputCedula.value.trim();

                    // 1. Limpiar todos los campos antes de hacer consulta
                    limpiarCamposFormulario();

                    if (!cedula) {
                        alert('Por favor ingrese una cédula antes de buscar.');
                        return;
                    }

                    // UI: Mostrar spinner
                    document.getElementById('icon-buscar').classList.add('d-none');
                    document.getElementById('spinner-buscar').classList.remove('d-none');
                    btnBuscar.disabled = true;

                    try {
                        const response = await fetch(`/asociados/buscar-tercero/${cedula}`);

                        if (response.ok) {
                            const res = await response.json();
                            if (res.status === 'success' && res.data) {
                                const data = res.data;

                                // Mapeo de campos de texto estándar
                                document.getElementById('nombre1').value = data.nom1 || '';
                                document.getElementById('nombre2').value = data.nom2 || '';
                                document.getElementById('apellido1').value = data.apl1 || '';
                                document.getElementById('apellido2').value = data.apl2 || '';

                                if (data.fec_nac) document.getElementById('fecha_nacimiento').value = data.fec_nac.split('T')[0];
                                if (data.fec_expcc) document.getElementById('fecha_expedicion').value = data.fec_expcc.split('T')[0];
                                
                                document.getElementById('estado_civil').value = data.est_civil || '';
                                document.getElementById('correo_pastor').value = data.email || '';
                                document.getElementById('celular_pastor').value = data.tel || '';
                                document.getElementById('whatsapp').value = data.cel || '';

                                document.getElementById('pais').value = data.pais || 'Colombia';
                                if (data.fec_minis) document.getElementById('fecha_afiliacion').value = data.fec_minis.split('T')[0];

                                document.getElementById('cedula_esposa').value = data.id_conyuge || '';
                                document.getElementById('nombre_esposa').value = data.nom_conyug || '';
                                document.getElementById('correo_esposa').value = data.mail_conyu || '';
                                document.getElementById('celular_esposa').value = data.tel1 || '';

                                // Mapeo de campos Datalist con IDs (Se asigna el ID oculto y se visualiza el Nombre)
                                setDatalistValue('hidden_distrito_actual', 'input_distrito_actual', 'distritos_list', data.cod_dist || '');
                                setDatalistValue('hidden_iglesia_actual', 'input_iglesia_actual', 'congregaciones_list', data.congrega || '');
                                setDatalistValue('hidden_lugar_expedicion', 'input_lugar_expedicion', 'ciudades_list', data.lugar_expcc || '');

                                // Feedback visual de éxito
                                inputCedula.classList.add('is-valid');
                            } else {
                                alert('No se encontró información en la base de datos maestra para esta cédula. Puede continuar digitando los datos manualmente.');
                            }
                        }
                    } catch (error) {
                        console.error('Error buscando cédula:', error);
                        alert('Ocurrió un error al intentar consultar la base de datos.');
                    } finally {
                        // Restaurar botón
                        document.getElementById('icon-buscar').classList.remove('d-none');
                        document.getElementById('spinner-buscar').classList.add('d-none');
                        btnBuscar.disabled = false;
                    }
                });

                // 2. LÓGICA DEL FORMULARIO Y MODAL DE SINCRONIZACIÓN
                const form = document.getElementById('form-expediente');
                const syncModal = new bootstrap.Modal(document.getElementById('syncModal'));
                let isSyncHandled = false;

                form.addEventListener('submit', function (e) {
                    if (!isSyncHandled) {
                        e.preventDefault();
                        if (this.checkValidity()) {
                            syncModal.show();
                        } else {
                            this.reportValidity();
                        }
                    }
                });

                function triggerFinalSubmit() {
                    isSyncHandled = true;
                    
                    const btn = document.getElementById('btn-submit');
                    const text = document.getElementById('btn-text');
                    const spinner = document.getElementById('btn-spinner');

                    btn.classList.add('disabled');
                    btn.style.pointerEvents = 'none';
                    text.classList.add('d-none');
                    spinner.classList.remove('d-none');

                    form.submit();
                }

                document.getElementById('btn-deny-sync').addEventListener('click', function () {
                    document.getElementById('input_sincronizar_tercero').value = "0";
                    syncModal.hide();
                    triggerFinalSubmit();
                });

                document.getElementById('btn-confirm-sync').addEventListener('click', function () {
                    document.getElementById('input_sincronizar_tercero').value = "1";
                    syncModal.hide();
                    triggerFinalSubmit();
                });
            });
        </script>
    @endpush

    @include('asociados.partials.styles')
</x-base-layout>