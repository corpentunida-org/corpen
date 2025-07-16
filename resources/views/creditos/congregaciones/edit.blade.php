{{-- resources/views/creditos/congregaciones/edit.blade.php --}}

<x-base-layout>
    @section('titlepage', 'Editar Congregación')

    {{-- Encabezado con el nombre de la congregación --}}
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-success text-white">
                            <i class="bi bi-church"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                {{-- Aquí usamos la relación para mostrar el nombre, igual que en el index --}}
                                {{ $congregacion->nombre }}
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Código: {{ $congregacion->codigo }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulario dentro de una tarjeta con pestañas --}}
    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header p-0">
                <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item flex-fill border-top" role="presentation">
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab" data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información General
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">
                    <div class="card stretch stretch-full">
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <h5 class="fw-bold">¡Error! Por favor, corrige los siguientes problemas:</h5>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('creditos.congregaciones.update', $congregacion->codigo) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código</label>
                                        <input type="text" class="form-control" value="{{ $congregacion->codigo }}" readonly disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre del Templo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nombre" value="{{ old('nombre', $congregacion->nombre) }}" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Cédula Pastor <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="pastor" value="{{ old('pastor', $congregacion->pastor ) }}" required>
                                    </div>
                                    
                                    <!-- ================================================================== -->
                                    <!-- CÓDIGO CORREGIDO: Campo "Clase"                                     -->
                                    <!-- ================================================================== -->
                                    <div class="col-md-4 mb-3">
                                        <label for="clase" class="form-label">Clase <span class="text-danger">*</span></label>
                                        {{-- Se cambia input por select para manejar la relación --}}
                                        <select class="form-select" id="clase" name="clase" required>
                                            {{-- Opción por defecto no seleccionable --}}
                                            <option value="" disabled>Seleccione una clase...</option>
                                            
                                            {{-- Iteramos sobre la variable $clases que pasamos desde el controlador --}}
                                            @foreach($clases as $clase_item)
                                                <option value="{{ $clase_item->id }}" 
                                                    {{-- Comparamos el ID de la clase actual del bucle con el ID que tiene la congregación.
                                                         Si coinciden, se añade el atributo 'selected' para que aparezca pre-seleccionada.
                                                         La función old() se usa para recuperar el valor si falla la validación. --}}
                                                    @if(old('clase', $congregacion->clase) == $clase_item->id) selected @endif>
                                                    
                                                    {{-- Mostramos el nombre de la clase, que es lo que el usuario verá --}}
                                                    {{ $clase_item->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- ================================================================== -->
                                    <!-- FIN DEL CÓDIGO CORREGIDO                                           -->
                                    <!-- ================================================================== -->

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" name="estado" required>
                                            <option value="A" {{ old('estado', $congregacion->estado) == 'A' ? 'selected' : '' }}>Activo</option>
                                            <option value="I" {{ old('estado', $congregacion->estado) == 'I' ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Municipio <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="municipio" value="{{ old('municipio', $congregacion->municipio) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <input type="text" class="form-control" name="direccion" value="{{ old('direccion', $congregacion->direccion) }}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" name="telefono" value="{{ old('telefono', $congregacion->telefono) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Celular</label>
                                        <input type="tel" class="form-control" name="celular" value="{{ old('celular', $congregacion->celular) }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Distrito</label>
                                        <input type="text" class="form-control" name="distrito" value="{{ old('distrito', $congregacion->distrito) }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha Apertura</label>
                                        <input type="date" class="form-control" name="apertura" value="{{ old('apertura', $congregacion->apertura ? \Carbon\Carbon::parse($congregacion->apertura)->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha Cierre</label>
                                        <input type="date" class="form-control" name="cierre" value="{{ old('cierre', $congregacion->cierre ? \Carbon\Carbon::parse($congregacion->cierre)->format('Y-m-d') : '') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" name="observacion" rows="3">{{ old('observacion', $congregacion->observacion) }}</textarea>
                                </div>

                                <div class="d-flex flex-row-reverse gap-2 mt-4">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="feather-save me-2"></i>
                                        <span>Actualizar Congregación</span>
                                    </button>
                                    <a href="{{ route('creditos.congregaciones.index') }}" class="btn btn-light">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>