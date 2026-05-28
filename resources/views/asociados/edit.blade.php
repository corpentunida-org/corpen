<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">ECM - Auditoría</span>
                <h1 class="main-title">Actualizar Expediente</h1>
                <p class="main-subtitle">Asociado: {{ $maeAsociado->nombre_completo }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.show', $maeAsociado->id) }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left"></i> <span>Cancelar Edición</span>
                </a>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <strong><i class="fas fa-exclamation-circle"></i> Revise los siguientes errores:</strong>
                <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="show-card-corp">
            <div class="show-body">
                <form action="{{ route('asociados.maestro.update', $maeAsociado->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-section mb-5">
                        <h4 class="mb-3 text-secondary border-bottom pb-2"><i class="fas fa-id-card me-2"></i> 1. Identidad y Demográficos</h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="label text-muted mb-1">Cédula <span class="text-danger">*</span></label>
                                <input type="text" name="cedula" class="form-control corporate-input" value="{{ old('cedula', $maeAsociado->cedula) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="label text-muted mb-1">Lugar Expedición</label>
                                <input type="text" name="lugar_expedicion_cedula" class="form-control corporate-input" value="{{ old('lugar_expedicion_cedula', $maeAsociado->lugar_expedicion_cedula) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="label text-muted mb-1">Fecha Expedición</label>
                                <input type="date" name="fecha_expedicion" class="form-control corporate-input" value="{{ old('fecha_expedicion', $maeAsociado->fecha_expedicion ? $maeAsociado->fecha_expedicion->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="label text-muted mb-1">Estado Civil</label>
                                <select name="estado_civil" class="form-select corporate-input">
                                    <option value="">Seleccione...</option>
                                    @foreach(['Soltero'=>'Soltero(a)', 'Casado'=>'Casado(a)', 'Viudo'=>'Viudo(a)'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('estado_civil', $maeAsociado->estado_civil) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3"><label class="label text-muted mb-1">Primer Nombre <span class="text-danger">*</span></label><input type="text" name="nombre1" class="form-control corporate-input" value="{{ old('nombre1', $maeAsociado->nombre1) }}" required></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Segundo Nombre</label><input type="text" name="nombre2" class="form-control corporate-input" value="{{ old('nombre2', $maeAsociado->nombre2) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Primer Apellido <span class="text-danger">*</span></label><input type="text" name="apellido1" class="form-control corporate-input" value="{{ old('apellido1', $maeAsociado->apellido1) }}" required></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Segundo Apellido</label><input type="text" name="apellido2" class="form-control corporate-input" value="{{ old('apellido2', $maeAsociado->apellido2) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Fecha Nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control corporate-input" value="{{ old('fecha_nacimiento', $maeAsociado->fecha_nacimiento ? $maeAsociado->fecha_nacimiento->format('Y-m-d') : '') }}"></div>
                        </div>
                    </div>

                    <div class="form-section mb-5">
                        <h4 class="mb-3 text-secondary border-bottom pb-2"><i class="fas fa-address-book me-2"></i> 2. Contacto y Núcleo Familiar</h4>
                        <div class="row g-3">
                            <div class="col-md-4"><label class="label text-muted mb-1">Correo Pastor</label><input type="email" name="correo_pastor" class="form-control corporate-input" value="{{ old('correo_pastor', $maeAsociado->correo_pastor) }}"></div>
                            <div class="col-md-4"><label class="label text-muted mb-1">Celular Pastor</label><input type="text" name="celular_pastor" class="form-control corporate-input" value="{{ old('celular_pastor', $maeAsociado->celular_pastor) }}"></div>
                            <div class="col-md-4"><label class="label text-muted mb-1">WhatsApp</label><input type="text" name="whatsapp" class="form-control corporate-input" value="{{ old('whatsapp', $maeAsociado->whatsapp) }}"></div>
                            <div class="col-md-3 mt-4"><label class="label text-muted mb-1">Cédula Esposa</label><input type="text" name="cedula_esposa" class="form-control corporate-input" value="{{ old('cedula_esposa', $maeAsociado->cedula_esposa) }}"></div>
                            <div class="col-md-3 mt-4"><label class="label text-muted mb-1">Nombre Esposa</label><input type="text" name="nombre_esposa" class="form-control corporate-input" value="{{ old('nombre_esposa', $maeAsociado->nombre_esposa) }}"></div>
                            <div class="col-md-3 mt-4"><label class="label text-muted mb-1">Celular Esposa</label><input type="text" name="celular_esposa" class="form-control corporate-input" value="{{ old('celular_esposa', $maeAsociado->celular_esposa) }}"></div>
                            <div class="col-md-3 mt-4"><label class="label text-muted mb-1">Correo Esposa</label><input type="email" name="correo_esposa" class="form-control corporate-input" value="{{ old('correo_esposa', $maeAsociado->correo_esposa) }}"></div>
                        </div>
                    </div>

                    <div class="form-section mb-5">
                        <h4 class="mb-3 text-secondary border-bottom pb-2"><i class="fas fa-church me-2"></i> 3. Información Ministerial</h4>
                        <div class="row g-3">
                            <div class="col-md-3"><label class="label text-muted mb-1">Distrito Actual</label><input type="text" name="distrito_actual" class="form-control corporate-input" value="{{ old('distrito_actual', $maeAsociado->distrito_actual) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Ciudad Distrito</label><input type="text" name="ciudad_distrito" class="form-control corporate-input" value="{{ old('ciudad_distrito', $maeAsociado->ciudad_distrito) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Iglesia Actual</label><input type="text" name="iglesia_actual" class="form-control corporate-input" value="{{ old('iglesia_actual', $maeAsociado->iglesia_actual) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Dirección Sede</label><input type="text" name="direccion_distrito" class="form-control corporate-input" value="{{ old('direccion_distrito', $maeAsociado->direccion_distrito) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">País</label><input type="text" name="pais" class="form-control corporate-input" value="{{ old('pais', $maeAsociado->pais) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Rol / Especificación</label><input type="text" name="especificacion" class="form-control corporate-input" value="{{ old('especificacion', $maeAsociado->especificacion) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Licencia Pastoral</label><input type="text" name="licencia" class="form-control corporate-input" value="{{ old('licencia', $maeAsociado->licencia) }}"></div>
                            <div class="col-md-3">
                                <label class="label text-muted mb-1">Estado del Pastor</label>
                                <select name="estado_pastor" class="form-select corporate-input">
                                    @foreach(['Activo', 'Retirado', 'Licencia', 'Suspendido'] as $estado)
                                        <option value="{{ $estado }}" {{ old('estado_pastor', $maeAsociado->estado_pastor) == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Fecha Afiliación</label><input type="date" name="fecha_afiliacion" class="form-control corporate-input" value="{{ old('fecha_afiliacion', $maeAsociado->fecha_afiliacion ? $maeAsociado->fecha_afiliacion->format('Y-m-d') : '') }}"></div>
                        </div>
                    </div>

                    <div class="form-section mb-5 bg-light p-4 rounded border">
                        <h4 class="mb-3 text-secondary border-bottom pb-2 border-secondary"><i class="fas fa-file-archive me-2"></i> 4. Archivo Físico y Digital</h4>
                        
                        <div class="row g-2 mb-4">
                            @foreach(['doc_formulario_afiliacion'=>'Form. Afiliación','doc_autorizacion_datos'=>'Habeas Data','doc_cedula_pastor'=>'CC Pastor','doc_cedula_esposa'=>'CC Esposa','doc_licencia_pastoral'=>'Licencia','doc_registro_matrimonio'=>'Reg. Matrimonio','doc_id_hijos'=>'ID Hijos'] as $campo => $label)
                            <div class="col-md-3">
                                <label class="label text-muted mb-1" style="font-size:0.8rem;">{{ $label }}</label>
                                <select name="{{ $campo }}" class="form-select form-select-sm corporate-input">
                                    @foreach(['Pendiente', 'Entregado', 'No Aplica'] as $opcion)
                                        <option value="{{ $opcion }}" {{ old($campo, $maeAsociado->$campo) == $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endforeach
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3"><label class="label text-muted mb-1">Radicado</label><input type="text" name="radicado" class="form-control corporate-input" value="{{ old('radicado', $maeAsociado->radicado) }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Ubicación</label><input type="text" name="ubicacion_carpeta" class="form-control corporate-input" value="{{ old('ubicacion_carpeta', $maeAsociado->ubicacion_carpeta) }}"></div>
                            <div class="col-md-2"><label class="label text-muted mb-1">Nº Caja</label><input type="text" name="numero_caja" class="form-control corporate-input" value="{{ old('numero_caja', $maeAsociado->numero_caja) }}"></div>
                            <div class="col-md-2"><label class="label text-muted mb-1">Folios</label><input type="number" name="cantidad_folios" class="form-control corporate-input" value="{{ old('cantidad_folios', $maeAsociado->cantidad_folios) }}"></div>
                            <div class="col-md-2">
                                <label class="label text-muted mb-1">Est. Papel</label>
                                <select name="estado_conservacion" class="form-select corporate-input">
                                    @foreach(['Bueno', 'Regular', 'Malo'] as $est)
                                        <option value="{{ $est }}" {{ old('estado_conservacion', $maeAsociado->estado_conservacion) == $est ? 'selected' : '' }}>{{ $est }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Ingreso Archivo</label><input type="date" name="fecha_ingreso_archivo" class="form-control corporate-input" value="{{ old('fecha_ingreso_archivo', $maeAsociado->fecha_ingreso_archivo ? $maeAsociado->fecha_ingreso_archivo->format('Y-m-d') : '') }}"></div>
                            <div class="col-md-3"><label class="label text-muted mb-1">Custodia</label><input type="text" name="custodia_actual" class="form-control corporate-input" value="{{ old('custodia_actual', $maeAsociado->custodia_actual) }}"></div>
                            <div class="col-md-6"><label class="label text-muted mb-1">Obs. Archivo</label><input type="text" name="observaciones_archivo" class="form-control corporate-input" value="{{ old('observaciones_archivo', $maeAsociado->observaciones_archivo) }}"></div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-12 mb-2">
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="escaneado" id="es1" value="1" {{ old('escaneado', $maeAsociado->escaneado) ? 'checked' : '' }}><label class="form-check-label fw-bold" for="es1">Escaneado</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="cargado_ecm" id="ec1" value="1" {{ old('cargado_ecm', $maeAsociado->cargado_ecm) ? 'checked' : '' }}><label class="form-check-label fw-bold text-primary" for="ec1">Cargado ECM</label></div>
                                <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="validado_archivo" id="va1" value="1" {{ old('validado_archivo', $maeAsociado->validado_archivo) ? 'checked' : '' }}><label class="form-check-label fw-bold text-success" for="va1">Validado</label></div>
                            </div>
                            <div class="col-md-12"><label class="label text-muted mb-1">URL Plataforma ECM</label><input type="url" name="ubicacion_ecm_link" class="form-control corporate-input" value="{{ old('ubicacion_ecm_link', $maeAsociado->ubicacion_ecm_link) }}"></div>
                        </div>
                    </div>

                    <div class="form-section mb-4 row">
                        <div class="col-md-3">
                            <label class="label text-muted mb-1 fw-bold">Estado del Expediente</label>
                            <select name="estado" class="form-select corporate-input fw-bold bg-light">
                                <option value="Activo" {{ old('estado', $maeAsociado->estado) == 'Activo' ? 'selected' : '' }}>ACTIVO</option>
                                <option value="Inactivo" {{ old('estado', $maeAsociado->estado) == 'Inactivo' ? 'selected' : '' }}>INACTIVO</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="label text-muted mb-1 fw-bold">Observaciones Generales</label>
                            <textarea name="observaciones_generales" class="form-control corporate-input" rows="2">{{ old('observaciones_generales', $maeAsociado->observaciones_generales) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                        <button type="submit" class="btn-corporate-black px-5 py-2">
                            <i class="fas fa-sync-alt me-2"></i> Actualizar Registro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('asociados.partials.styles')
</x-base-layout>