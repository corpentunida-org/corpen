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
                <strong><i class="fas fa-exclamation-circle me-2"></i> Por favor corrija los errores marcados en el formulario.</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('asociados.maestro.store') }}" method="POST" id="form-expediente">
            @csrf
            
            {{-- Input oculto para definir si se sincroniza con MaeTerceros --}}
            <input type="hidden" name="sincronizar_tercero" id="input_sincronizar_tercero" value="1">

            {{-- ==========================================
                 SECCIÓN 1: IDENTIDAD Y DEMOGRÁFICOS
                 ========================================== --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="text-secondary"><i class="fas fa-id-card me-2 text-primary"></i> 1. Identidad y Demográficos</h5>
                    <hr class="text-muted">
                </div>
                <div class="card-body pt-1">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Cédula <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}" required>
                                <button class="btn btn-outline-primary" type="button" id="btn-buscar-cedula">
                                    <i class="fas fa-search" id="icon-buscar"></i>
                                    <i class="fas fa-spinner fa-spin d-none" id="spinner-buscar"></i>
                                </button>
                            </div>
                            @error('cedula') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Lugar de Expedición</label>
                            <input type="text" name="lugar_expedicion_cedula" id="lugar_expedicion_cedula" class="form-control @error('lugar_expedicion_cedula') is-invalid @enderror" value="{{ old('lugar_expedicion_cedula') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Fecha de Expedición</label>
                            <input type="date" name="fecha_expedicion" id="fecha_expedicion" class="form-control @error('fecha_expedicion') is-invalid @enderror" value="{{ old('fecha_expedicion') }}">
                            @error('fecha_expedicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Estado Civil</label>
                            <select name="estado_civil" id="estado_civil" class="form-select @error('estado_civil') is-invalid @enderror">
                                <option value="">Seleccione...</option>
                                <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero(a)</option>
                                <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado(a)</option>
                                <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo(a)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Primer Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre1" id="nombre1" class="form-control @error('nombre1') is-invalid @enderror" value="{{ old('nombre1') }}" required>
                            @error('nombre1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Segundo Nombre</label>
                            <input type="text" name="nombre2" id="nombre2" class="form-control" value="{{ old('nombre2') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Primer Apellido <span class="text-danger">*</span></label>
                            <input type="text" name="apellido1" id="apellido1" class="form-control @error('apellido1') is-invalid @enderror" value="{{ old('apellido1') }}" required>
                            @error('apellido1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Segundo Apellido</label>
                            <input type="text" name="apellido2" id="apellido2" class="form-control" value="{{ old('apellido2') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}">
                            @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECCIÓN 2: CONTACTO Y NÚCLEO FAMILIAR
                 ========================================== --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="text-secondary"><i class="fas fa-users me-2 text-primary"></i> 2. Contacto y Núcleo Familiar</h5>
                    <hr class="text-muted">
                </div>
                <div class="card-body pt-1">
                    <h6 class="text-muted fw-bold mb-3">Datos del Pastor</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Correo Electrónico</label>
                            <input type="email" name="correo_pastor" id="correo_pastor" class="form-control @error('correo_pastor') is-invalid @enderror" value="{{ old('correo_pastor') }}">
                            @error('correo_pastor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Celular Principal</label>
                            <input type="text" name="celular_pastor" id="celular_pastor" class="form-control" value="{{ old('celular_pastor') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                        </div>
                    </div>

                    <h6 class="text-muted fw-bold mb-3">Datos de la Esposa (Si aplica)</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Cédula Esposa</label>
                            <input type="text" name="cedula_esposa" id="cedula_esposa" class="form-control" value="{{ old('cedula_esposa') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Nombre Completo</label>
                            <input type="text" name="nombre_esposa" id="nombre_esposa" class="form-control" value="{{ old('nombre_esposa') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Celular Esposa</label>
                            <input type="text" name="celular_esposa" id="celular_esposa" class="form-control" value="{{ old('celular_esposa') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Correo Esposa</label>
                            <input type="email" name="correo_esposa" id="correo_esposa" class="form-control @error('correo_esposa') is-invalid @enderror" value="{{ old('correo_esposa') }}">
                            @error('correo_esposa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECCIÓN 3: INFORMACIÓN MINISTERIAL
                 ========================================== --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="text-secondary"><i class="fas fa-church me-2 text-primary"></i> 3. Información Ministerial</h5>
                    <hr class="text-muted">
                </div>
                <div class="card-body pt-1">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">País de Servicio</label>
                            <input type="text" name="pais" id="pais" class="form-control" value="{{ old('pais', 'Colombia') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Distrito Actual</label>
                            <input type="text" name="distrito_actual" id="distrito_actual" class="form-control" value="{{ old('distrito_actual') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Ciudad del Distrito</label>
                            <input type="text" name="ciudad_distrito" id="ciudad_distrito" class="form-control" value="{{ old('ciudad_distrito') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Dirección Sede</label>
                            <input type="text" name="direccion_distrito" id="direccion_distrito" class="form-control" value="{{ old('direccion_distrito') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Iglesia Actual</label>
                            <input type="text" name="iglesia_actual" id="iglesia_actual" class="form-control" value="{{ old('iglesia_actual') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Rol / Especificación</label>
                            <input type="text" name="especificacion" class="form-control" value="{{ old('especificacion') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Licencia Pastoral</label>
                            <input type="text" name="licencia" class="form-control" value="{{ old('licencia') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Fecha de Afiliación</label>
                            <input type="date" name="fecha_afiliacion" id="fecha_afiliacion" class="form-control" value="{{ old('fecha_afiliacion') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Estado del Pastor</label>
                            <select name="estado_pastor" class="form-select @error('estado_pastor') is-invalid @enderror">
                                <option value="Activo" {{ old('estado_pastor') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Retirado" {{ old('estado_pastor') == 'Retirado' ? 'selected' : '' }}>Retirado</option>
                                <option value="Licencia" {{ old('estado_pastor') == 'Licencia' ? 'selected' : '' }}>Licencia</option>
                                <option value="Suspendido" {{ old('estado_pastor') == 'Suspendido' ? 'selected' : '' }}>Suspendido</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECCIÓN 4: GESTIÓN DE ARCHIVO FÍSICO Y DIGITAL
                 ========================================== --}}
            <div class="card shadow-sm border-0 bg-light mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                    <h5 class="text-secondary"><i class="fas fa-file-archive me-2 text-primary"></i> 4. Control de Archivo y ECM</h5>
                    <hr class="text-muted">
                </div>
                <div class="card-body pt-1">
                    
                    {{-- Soportes Documentales (Anexos) --}}
                    <h6 class="text-muted fw-bold mb-3">Soportes Documentales Entregados</h6>
                    <div class="row g-2 mb-4">
                        @php
                            $documentos = [
                                'doc_formulario_afiliacion' => 'Form. Afiliación',
                                'doc_autorizacion_datos'    => 'Habeas Data',
                                'doc_cedula_pastor'         => 'CC Pastor',
                                'doc_cedula_esposa'         => 'CC Esposa',
                                'doc_licencia_pastoral'     => 'Licencia Pastoral',
                                'doc_registro_matrimonio'   => 'Reg. Matrimonio',
                                'doc_id_hijos'              => 'Docs Hijos'
                            ];
                        @endphp
                        
                        @foreach($documentos as $campo => $label)
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1" style="font-size:0.85rem;">{{ $label }}</label>
                            <select name="{{ $campo }}" class="form-select form-select-sm">
                                <option value="Pendiente" {{ old($campo) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="Entregado" {{ old($campo) == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                                <option value="No Aplica" {{ old($campo) == 'No Aplica' ? 'selected' : '' }}>No Aplica</option>
                            </select>
                        </div>
                        @endforeach
                    </div>

                    {{-- Archivo Físico --}}
                    <h6 class="text-muted fw-bold mb-3 mt-4">Localización de Archivo Físico</h6>
                    <div class="row g-3 mb-4 bg-white p-3 rounded border">
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Nº Radicado</label>
                            <input type="text" name="radicado" class="form-control" value="{{ old('radicado') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Ubicación Carpeta</label>
                            <input type="text" name="ubicacion_carpeta" class="form-control" value="{{ old('ubicacion_carpeta') }}" placeholder="Ej. Estante A, Fila 2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted mb-1">Número de Caja</label>
                            <input type="text" name="numero_caja" class="form-control" value="{{ old('numero_caja') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted mb-1">Cant. Folios</label>
                            <input type="number" name="cantidad_folios" class="form-control @error('cantidad_folios') is-invalid @enderror" value="{{ old('cantidad_folios') }}" min="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted mb-1">Estado Papel</label>
                            <select name="estado_conservacion" class="form-select">
                                <option value="Bueno" {{ old('estado_conservacion') == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                                <option value="Regular" {{ old('estado_conservacion') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                <option value="Malo" {{ old('estado_conservacion') == 'Malo' ? 'selected' : '' }}>Malo</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Fecha Ingreso Archivo</label>
                            <input type="date" name="fecha_ingreso_archivo" class="form-control" value="{{ old('fecha_ingreso_archivo') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1">Custodia Actual</label>
                            <input type="text" name="custodia_actual" class="form-control" value="{{ old('custodia_actual', 'Archivo Central') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-1">Observaciones Archivo Físico</label>
                            <input type="text" name="observaciones_archivo" class="form-control" value="{{ old('observaciones_archivo') }}">
                        </div>
                    </div>

                    {{-- Archivo Digital (ECM) --}}
                    <h6 class="text-muted fw-bold mb-3 mt-4">Integración ECM (Digital)</h6>
                    <div class="row g-3">
                        <div class="col-md-12 mb-2">
                            <div class="d-flex flex-wrap gap-4 bg-white p-3 rounded border">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="escaneado" id="es1" value="1" {{ old('escaneado') ? 'checked' : '' }}>
                                    <label class="form-check-label text-secondary fw-bold" for="es1">Escaneado</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="cargado_ecm" id="ec1" value="1" {{ old('cargado_ecm') ? 'checked' : '' }}>
                                    <label class="form-check-label text-primary fw-bold" for="ec1">Cargado en Plataforma ECM</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="validado_archivo" id="va1" value="1" {{ old('validado_archivo') ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-bold" for="va1">Expediente Validado</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted mb-1">Enlace / URL de Plataforma ECM</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="url" name="ubicacion_ecm_link" class="form-control @error('ubicacion_ecm_link') is-invalid @enderror" value="{{ old('ubicacion_ecm_link') }}" placeholder="https://...">
                            </div>
                            @error('ubicacion_ecm_link') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 SECCIÓN 5: METADATOS FINALES Y OBSERVACIONES
                 ========================================== --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted mb-1 fw-bold">Estado del Expediente</label>
                            <select name="estado" class="form-select bg-light fw-bold text-secondary">
                                <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>ACTIVO</option>
                                <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>INACTIVO</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label text-muted mb-1 fw-bold">Observaciones Generales</label>
                            <textarea name="observaciones_generales" class="form-control" rows="2" placeholder="Cualquier nota adicional sobre la apertura de este expediente...">{{ old('observaciones_generales') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 BOTÓN DE ENVÍO
                 ========================================== --}}
            <div class="sticky-bottom bg-white p-3 border-top shadow-lg d-flex justify-content-end align-items-center" style="bottom: 0; z-index: 1000;">
                <button type="submit" id="btn-submit" class="btn btn-dark px-5 py-2 fw-bold" style="min-width: 250px;">
                    <span id="btn-text"><i class="fas fa-save me-2"></i> Consolidar Expediente</span>
                    <span id="btn-spinner" class="d-none"><i class="fas fa-spinner fa-spin me-2"></i> Procesando...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ==========================================
         MODAL DE CONFIRMACIÓN DE SINCRONIZACIÓN
         ========================================== --}}
    <div class="modal fade" id="syncModal" tabindex="-1" aria-labelledby="syncModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="syncModalLabel"><i class="fas fa-sync-alt me-2"></i> Sincronización de Datos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Desea actualizar/sincronizar la información de este asociado con la <strong>Base de Datos Maestra de Terceros (MaeTerceros)</strong>?</p>
                    <p class="text-muted small"><i class="fas fa-info-circle"></i> Si selecciona "Sí", los datos personales y de contacto sobrescribirán el registro actual en el directorio global.</p>
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
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. LÓGICA DE BÚSQUEDA DE CÉDULA VIA AJAX
            const btnBuscar = document.getElementById('btn-buscar-cedula');
            const inputCedula = document.getElementById('cedula');

            btnBuscar.addEventListener('click', async function() {
                const cedula = inputCedula.value.trim();
                if(!cedula) {
                    alert('Por favor ingrese una cédula antes de buscar.');
                    return;
                }

                // UI: Mostrar spinner en el botón de búsqueda
                document.getElementById('icon-buscar').classList.add('d-none');
                document.getElementById('spinner-buscar').classList.remove('d-none');
                btnBuscar.disabled = true;

                try {
                    // Nota: Asegúrate de crear esta ruta en web.php que busque en el modelo MaeTerceros
                    // Ejemplo: Route::get('/terceros/buscar/{cedula}', [TerceroController::class, 'buscar']);
                    const response = await fetch(`/asociados/buscar-tercero/${cedula}`);
                    
                    if(response.ok) {
                        const res = await response.json();
                        if(res.status === 'success' && res.data) {
                            const data = res.data;
                            
                            // Mapeo automático de los inputs basado en tu modelo MaeTerceros
                            document.getElementById('nombre1').value = data.nom1 || '';
                            document.getElementById('nombre2').value = data.nom2 || '';
                            document.getElementById('apellido1').value = data.apl1 || '';
                            document.getElementById('apellido2').value = data.apl2 || '';
                            
                            if(data.fec_nac) document.getElementById('fecha_nacimiento').value = data.fec_nac.split('T')[0];
                            if(data.fec_expcc) document.getElementById('fecha_expedicion').value = data.fec_expcc.split('T')[0];
                            
                            document.getElementById('lugar_expedicion_cedula').value = data.lugar_expcc || '';
                            document.getElementById('estado_civil').value = data.est_civil || '';
                            
                            document.getElementById('correo_pastor').value = data.email || '';
                            document.getElementById('celular_pastor').value = data.tel || '';
                            document.getElementById('whatsapp').value = data.cel || '';
                            
                            document.getElementById('distrito_actual').value = data.cod_dist || '';
                            document.getElementById('iglesia_actual').value = data.congrega || '';
                            document.getElementById('pais').value = data.pais || 'Colombia';
                            if(data.fec_minis) document.getElementById('fecha_afiliacion').value = data.fec_minis.split('T')[0];

                            document.getElementById('cedula_esposa').value = data.id_conyuge || '';
                            document.getElementById('nombre_esposa').value = data.nom_conyug || '';
                            document.getElementById('correo_esposa').value = data.mail_conyu || '';
                            document.getElementById('celular_esposa').value = data.tel1 || '';

                            // Feedback visual
                            inputCedula.classList.add('is-valid');
                        } else {
                            alert('No se encontró información en la base de datos maestra para esta cédula. Puede continuar digitando.');
                        }
                    }
                } catch (error) {
                    console.error('Error buscando cédula:', error);
                } finally {
                    // Restaurar botón de búsqueda
                    document.getElementById('icon-buscar').classList.remove('d-none');
                    document.getElementById('spinner-buscar').classList.add('d-none');
                    btnBuscar.disabled = false;
                }
            });


            // 2. LÓGICA DEL FORMULARIO Y MODAL DE SINCRONIZACIÓN
            const form = document.getElementById('form-expediente');
            const syncModal = new bootstrap.Modal(document.getElementById('syncModal'));
            let isSyncHandled = false; // Bandera para saber si ya pasamos por el modal

            form.addEventListener('submit', function(e) {
                // Si aún no hemos manejado la validación del modal, detenemos el envío
                if (!isSyncHandled) {
                    e.preventDefault();

                    // Validar primero si el formulario nativo está correcto
                    if(this.checkValidity()) {
                        syncModal.show(); // Mostramos el modal de confirmación
                    } else {
                        this.reportValidity(); // Muestra las alertas nativas de HTML5 de "campo requerido"
                    }
                }
            });

            // Función para disparar el envío real una vez decidido
            function triggerFinalSubmit() {
                isSyncHandled = true; // Evita el loop infinito
                
                // UX: Prevenir doble clic y dar feedback visual al usuario
                const btn = document.getElementById('btn-submit');
                const text = document.getElementById('btn-text');
                const spinner = document.getElementById('btn-spinner');
                
                btn.classList.add('disabled');
                btn.style.pointerEvents = 'none';
                text.classList.add('d-none');
                spinner.classList.remove('d-none');
                
                form.submit(); // Enviar el formulario
            }

            // Click en "No, guardar solo aquí"
            document.getElementById('btn-deny-sync').addEventListener('click', function() {
                document.getElementById('input_sincronizar_tercero').value = "0"; // Falso
                syncModal.hide();
                triggerFinalSubmit();
            });

            // Click en "Sí, sincronizar"
            document.getElementById('btn-confirm-sync').addEventListener('click', function() {
                document.getElementById('input_sincronizar_tercero').value = "1"; // Verdadero
                syncModal.hide();
                triggerFinalSubmit();
            });
        });
    </script>
    @endpush

    @include('asociados.partials.styles')
</x-base-layout>