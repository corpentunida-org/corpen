<x-base-layout>
    @section('titlepage', 'Crear Tercero')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div>
                        <h3 class="mb-0">Nuevo Tercero</h3>
                        <span class="text-muted">Complete los datos requeridos.</span>
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
            {{-- Información Básica --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBasica">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasica">
                        Información Básica
                    </button>
                </h2>
                <div id="collapseBasica" class="accordion-collapse collapse show" data-bs-parent="#accordionTercero">
                    <div class="accordion-body row g-3">
                        <div class="col-md-4">
                            <label for="cod_ter" class="form-label">Código / Identificación</label>
                            <input type="text" name="cod_ter" id="cod_ter" class="form-control" value="{{ old('cod_ter') }}" required>
                        </div>
                        <div class="col-md-8">
                            <label for="nombre" class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contacto --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingContacto">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContacto">
                        Información de Contacto
                    </button>
                </h2>
                <div id="collapseContacto" class="accordion-collapse collapse" data-bs-parent="#accordionTercero">
                    <div class="accordion-body row g-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dirección --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDireccion">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDireccion">
                        Dirección y Ubicación
                    </button>
                </h2>
                <div id="collapseDireccion" class="accordion-collapse collapse" data-bs-parent="#accordionTercero">
                    <div class="accordion-body row g-3">
                        <div class="col-md-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" class="form-control" value="{{ old('ciudad') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="departamento" class="form-label">Departamento</label>
                            <input type="text" name="departamento" id="departamento" class="form-control" value="{{ old('departamento') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Otros campos adicionales opcionales según tus $fillable --}}
            {{-- Puedes seguir agregando acordeones por secciones como en el edit.blade.php --}}
        </div>

        {{-- Botones --}}
        <div class="mt-4">
            <a href="{{ route('maestras.terceros.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Tercero</button>
        </div>
    </form>
</x-base-layout>
