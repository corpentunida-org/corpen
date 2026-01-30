{{-- 
    ESTILO: Vertical Zen (Single Column Flow)
    SOLUCIÓN: Máximo aprovechamiento de lectura horizontal y espaciado generoso.
--}}

<div class="animate-fade-in mx-auto" style="max-width: 900px;">

    {{-- SECCIÓN 1: IDENTIFICACIÓN --}}
    <div class="card border-0 shadow-sm mb-4 custom-card">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-1">Identificación del Cargo</h5>
                <p class="text-muted small">Define el rol y su ubicación en la estructura.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-9">
                    <label class="label-minimal">Nombre del Cargo</label>
                    <input type="text" name="nombre_cargo" class="form-control minimal-input" value="{{ old('nombre_cargo', $cargo->nombre_cargo ?? '') }}" placeholder="Ej: Director de Tecnología" required>
                </div>
                <div class="col-md-3">
                    <label class="label-minimal">Estado</label>
                    <select name="estado" class="form-select minimal-input">
                        <option value="1" {{ old('estado', $cargo->estado ?? 1) == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estado', $cargo->estado ?? 1) == 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="label-minimal">Área Funcional</label>
                    <select name="GDO_area_id" class="form-select minimal-input" required>
                        <option value="" disabled selected>Seleccione área...</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('GDO_area_id', $cargo->GDO_area_id ?? '') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="label-minimal">Líder / Responsable</label>
                    <select name="GDO_empleados_cedula" class="form-select minimal-input">
                        <option value="">(Sin asignar)</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->cedula }}" {{ old('GDO_empleados_cedula', $cargo->GDO_empleados_cedula ?? '') == $empleado->cedula ? 'selected' : '' }}>{{ $empleado->nombre1 }} {{ $empleado->apellido1 }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: CONDICIONES LABORALES --}}
    <div class="card border-0 shadow-sm mb-4 custom-card">
        <div class="card-body p-4 p-md-5">
            <h6 class="text-uppercase fw-bold text-muted mb-4 small" style="letter-spacing: 1px;">Condiciones y Remuneración</h6>
            
            <div class="row g-4 align-items-end">
                <div class="col-md-6">
                    <label class="label-minimal text-success">Salario Base Mensual</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light fw-bold">$</span>
                        <input type="text" id="salario_input" name="salario_base_visual" class="form-control minimal-input border-start-0" 
                               value="{{ old('salario_base', isset($cargo->salario_base) ? number_format($cargo->salario_base, 0, ',', '.') : '') }}" placeholder="0">
                        <input type="hidden" name="salario_base" id="salario_hidden" value="{{ old('salario_base', $cargo->salario_base ?? '') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="label-minimal">Jornada</label>
                    <input type="text" name="jornada" class="form-control minimal-input" value="{{ old('jornada', $cargo->jornada ?? '') }}" placeholder="Ej: Tiempo Completo">
                </div>
                
                <div class="col-12 mt-4">
                    <div class="p-3 rounded-3 bg-light border border-dashed text-center">
                        <label class="label-minimal d-block mb-2">Documentación Técnica (Manual de Funciones)</label>
                        <input type="file" name="manual_funciones" class="form-control form-control-sm border-0 bg-transparent mx-auto" style="max-width: 300px;" accept=".pdf">
                        @if(isset($cargo) && $cargo->manual_funciones)
                            <div class="mt-2">
                                <a href="{{ route('archivo.cargo.verManual', $cargo->id) }}" target="_blank" class="text-decoration-none small fw-bold text-primary">
                                    <i class="bi bi-file-earmark-check"></i> Ver PDF Actual Cargado
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: CONTACTO --}}
    <div class="card border-0 shadow-sm mb-5 custom-card">
        <div class="card-body p-4 p-md-5">
            <h6 class="text-uppercase fw-bold text-muted mb-4 small" style="letter-spacing: 1px;">Canales Corporativos</h6>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="label-minimal">Teléfono</label>
                    <input type="text" name="telefono_corporativo" class="form-control minimal-input" value="{{ old('telefono_corporativo', $cargo->telefono_corporativo ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="label-minimal">Celular</label>
                    <input type="text" name="celular_corporativo" class="form-control minimal-input" value="{{ old('celular_corporativo', $cargo->celular_corporativo ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="label-minimal">Extensión</label>
                    <input type="text" name="ext_corporativo" class="form-control minimal-input" value="{{ old('ext_corporativo', $cargo->ext_corporativo ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="label-minimal">E-mail Institucional</label>
                    <input type="email" name="correo_corporativo" class="form-control minimal-input" value="{{ old('correo_corporativo', $cargo->correo_corporativo ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="label-minimal">Gmail Corporativo</label>
                    <input type="email" name="gmail_corporativo" class="form-control minimal-input" value="{{ old('gmail_corporativo', $cargo->gmail_corporativo ?? '') }}">
                </div>
                <div class="col-12 mt-3">
                    <label class="label-minimal">Observaciones</label>
                    <textarea name="observacion" class="form-control minimal-input pt-2" style="height: 80px;">{{ old('observacion', $cargo->observacion ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-card {
        border-radius: 20px;
        background: #ffffff;
    }

    .minimal-input {
        background-color: #f8fafc !important;
        border: 1.5px solid #f1f5f9 !important;
        border-radius: 12px !important;
        height: 48px;
        transition: all 0.2s ease;
    }

    .minimal-input:focus {
        background-color: #fff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
    }

    .label-minimal {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .border-dashed { border: 2px dashed #e2e8f0 !important; }
</style>