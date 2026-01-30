{{-- Contenedor de campos optimizado para UX --}}
<div class="d-flex flex-column gap-4">

    {{-- SECCIÓN: Identificación Básica --}}
    <div class="row g-3">
        {{-- Campo Nombre con Icono --}}
        <div class="col-12">
            <div class="form-floating form-floating-custom">
                <input type="text" name="nombre" id="nombre" 
                       class="form-control border-0 bg-light shadow-none @error('nombre') is-invalid @enderror" 
                       placeholder="Nombre del área" 
                       value="{{ old('nombre', $area->nombre ?? '') }}" 
                       required 
                       style="border-radius: 12px; height: 60px;">
                <label for="nombre" class="text-muted small fw-bold">
                    <i class="bi bi-building-gear me-1"></i> NOMBRE DEL ÁREA
                </label>
                @error('nombre') 
                    <div class="invalid-feedback ps-2" style="font-size: 0.75rem;">{{ $message }}</div> 
                @enderror
            </div>
        </div>
        
        {{-- Campo Descripción con altura dinámica --}}
        <div class="col-12">
            <div class="form-floating">
                <textarea name="descripcion" id="descripcion" 
                          class="form-control border-0 bg-light shadow-none @error('descripcion') is-invalid @enderror" 
                          placeholder="Descripción" 
                          style="height: 120px; border-radius: 12px;">{{ old('descripcion', $area->descripcion ?? '') }}</textarea>
                <label for="descripcion" class="text-muted small fw-bold">
                    <i class="bi bi-chat-left-text me-1"></i> DESCRIPCIÓN O MISIÓN DEL ÁREA
                </label>
                @error('descripcion') 
                    <div class="invalid-feedback ps-2" style="font-size: 0.75rem;">{{ $message }}</div> 
                @enderror
            </div>
        </div>
    </div>

    {{-- SECCIÓN: Configuración y Liderazgo --}}
    <div class="row g-3">
        {{-- Selector de Estado con Badge Visual --}}
        <div class="col-md-5">
            <div class="form-floating">
                <select name="estado" id="estado" 
                        class="form-select border-0 bg-light shadow-none @error('estado') is-invalid @enderror" 
                        style="border-radius: 12px; height: 60px;">
                    <option value="activo" {{ old('estado', $area->estado ?? 'activo') == 'activo' ? 'selected' : '' }}>🟢 Activo</option>
                    <option value="inactivo" {{ old('estado', $area->estado ?? 'activo') == 'inactivo' ? 'selected' : '' }}>🔴 Inactivo</option>
                </select>
                <label for="estado" class="text-muted small fw-bold">ESTADO OPERATIVO</label>
                @error('estado') 
                    <div class="invalid-feedback ps-2" style="font-size: 0.75rem;">{{ $message }}</div> 
                @enderror
            </div>
        </div>

        {{-- Selector de Jefe con icono de jerarquía --}}
        <div class="col-md-7">
            <div class="form-floating">
                <select name="GDO_cargo_id" id="GDO_cargo_id" 
                        class="form-select border-0 bg-light shadow-none @error('GDO_cargo_id') is-invalid @enderror" 
                        style="border-radius: 12px; height: 60px;">
                    <option value="" disabled {{ old('GDO_cargo_id', $area->GDO_cargo_id ?? '') == '' ? 'selected' : '' }}>Selecciona un cargo directivo...</option>
                    @foreach($cargos as $cargo)
                        <option value="{{ $cargo->id }}" 
                            {{ old('GDO_cargo_id', $area->GDO_cargo_id ?? '') == $cargo->id ? 'selected' : '' }}>
                            💼 {{ $cargo->nombre_cargo }}
                        </option>
                    @endforeach
                </select>
                <label for="GDO_cargo_id" class="text-muted small fw-bold">CARGO DE COORDINACIÓN / JEFE</label>
                @error('GDO_cargo_id') 
                    <div class="invalid-feedback ps-2" style="font-size: 0.75rem;">{{ $message }}</div> 
                @enderror
            </div>
        </div>
    </div>

    {{-- NOTA DE AYUDA --}}
    <div class="bg-primary-subtle border-start border-primary border-4 p-3 rounded-3 mt-2">
        <div class="d-flex">
            <i class="bi bi-info-circle-fill text-primary me-3 fs-5"></i>
            <p class="small text-primary-emphasis mb-0">
                <strong>Consejo:</strong> Asegúrate de que el cargo de coordinación tenga un colaborador asignado para que aparezca correctamente en el organigrama visual.
            </p>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados para el formulario */
    .form-control:focus, .form-select:focus {
        background-color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1) !important;
        border: 1px solid #6366f1 !important;
    }
    
    .form-floating > label {
        padding-left: 1.25rem;
        transition: all 0.2s ease-in-out;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label,
    .form-floating > .form-select ~ label {
        color: #4338ca !important;
        font-weight: 800;
        letter-spacing: 0.05em;
    }
</style>