<x-base-layout>
    @section('titlepage', 'Crear Tercero')

    {{-- Encabezado de la página --}}
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div>
                        <h3 class="mb-0">Nuevo Tercero</h3>
                        <span class="text-muted">Complete los datos requeridos. Los campos marcados con (*) son obligatorios.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario de creación --}}
    <form method="POST" action="{{ route('maestras.terceros.store') }}">
        @csrf

        {{-- Acordeón con secciones --}}
        <div class="accordion" id="accordionTercero">

            {{-- SECCIÓN 1: Información Básica --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBasica">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasica" aria-expanded="true" aria-controls="collapseBasica">
                        Información Básica
                    </button>
                </h2>
                <div id="collapseBasica" class="accordion-collapse collapse show" aria-labelledby="headingBasica" data-bs-parent="#accordionTercero">
                    <div class="accordion-body row g-3">
                        
                        {{-- Columna para Código --}}
                        <div class="col-md-4">
                            <label for="cod_ter" class="form-label">Código / Identificación (*)</label>
                            <input type="text" name="cod_ter" id="cod_ter" class="form-control @error('cod_ter') is-invalid @enderror" value="{{ old('cod_ter') }}">
                            @error('cod_ter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Columna para Nombre --}}
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre Completo (*)</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Columna para el Tipo --}}
                        <div class="col-md-4">
                            <label for="tip_prv" class="form-label">Tipo (*)</label>
                            
                            <select class="form-select @error('tip_prv') is-invalid @enderror" name="tip_prv" id="tip_prv">
                                <option value="" disabled selected>Seleccione un tipo...</option>
                                
                                @foreach($tipos as $m)
                                    <option value="{{ $m->codigo }}" {{ old('tip_prv') == $m->codigo ? 'selected' : '' }}>
                                        {{ $m->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            
                            @error('tip_prv')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: Contacto --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingContacto">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContacto" aria-expanded="false" aria-controls="collapseContacto">
                        Información de Contacto
                    </button>
                </h2>
                <div id="collapseContacto" class="accordion-collapse collapse" aria-labelledby="headingContacto" data-bs-parent="#accordionTercero">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: Dirección --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDireccion">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDireccion" aria-expanded="false" aria-controls="collapseDireccion">
                        Dirección y Ubicación
                    </button>
                </h2>
                <div id="collapseDireccion" class="accordion-collapse collapse" aria-labelledby="headingDireccion" data-bs-parent="#accordionTercero">
                    <div class="accordion-body row g-3">
                        <div class="col-md-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" class="form-control @error('ciudad') is-invalid @enderror" value="{{ old('ciudad') }}">
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="departamento" class="form-label">Departamento</label>
                            <input type="text" name="departamento" id="departamento" class="form-control @error('departamento') is-invalid @enderror" value="{{ old('departamento') }}">
                            @error('departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Botones de Acción --}}
        <div class="mt-4">
            <a href="{{ route('maestras.terceros.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Tercero</button>
        </div>
    </form>
</x-base-layout>