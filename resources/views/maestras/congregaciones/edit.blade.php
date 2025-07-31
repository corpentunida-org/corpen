{{-- resources/views/creditos/congregaciones/edit.blade.php --}}

<x-base-layout>
    @section('titlepage', 'Editar Congregación')

    {{-- 🔔 Alerta de advertencia si el pastor no existe --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- Encabezado con el nombre de la congregación --}}
    <div class="col-lg-12">

        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold text-dark">
                                {{ $congregacion->nombre }}
                            </div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">
                                Pastor: 
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
                        <a href="javascript:void(0);" class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#infoTab" role="tab" aria-selected="true">
                            Información General
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade p-4 active show" id="infoTab" role="tabpanel">

                <form method="POST" action="{{ route('maestras.congregacion.update', $congregacion->codigo) }}"
                    id="formUpdateCongregacion" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="codigo" value="{{ $congregacion->codigo }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Templo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="nombre"
                                value="{{ old('nombre', $congregacion->nombre) }}" required>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label"> CC Pastor Actual</label>
                            
                            <input type="number" class="form-control" name="pastor" id="cedulaPastor"
                                value="{{ old('pastor', $congregacion->pastor) }}" required> <br>

                            <input type="text" class="form-control" id="nombrePastor"
                                value="{{ $pastorSeleccionado?->nom_ter ?? 'No encontrado' }}" disabled>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CC Pastor Anterior</label>

                            <input type="number" class="form-control" name="pastorAnterior" id="cedulaPastorAnterior"
                                value="{{ old('pastorAnterior', $congregacion->pastorAnterior) }}"> <br>

                            <input type="text" class="form-control" id="nombrePastorAnterior"
                                value="{{ $pastorAnteriorSeleccionado?->nom_ter ?? 'No encontrado' }}" disabled>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Clase</label>
                            <select class="form-select" name="clase" required>
                                @foreach($clases as $c)
                                    <option value="{{ $c->id }}" {{ old('clase', $congregacion->clase) == $c->id ? 'selected' : '' }}>
                                        {{ $c->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select" name="estado" required>
                                <option value="1" {{ old('estado', $congregacion->estado) == 1 ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ old('estado', $congregacion->estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Municipio</label>
                            <select class="form-select" name="municipio" required>
                                <option value="" disabled {{ old('municipio', $congregacion->municipio) ? '' : 'selected' }}>Seleccione un municipio</option>
                                @foreach($municipios as $m)
                                    <option value="{{ $m->id }}" {{ old('municipio', $congregacion->municipio) == $m->id ? 'selected' : '' }}>
                                        {{ $m->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion"
                                value="{{ old('direccion', $congregacion->direccion) }}">
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono"
                                value="{{ old('telefono', $congregacion->telefono) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Celular</label>
                            <input type="tel" class="form-control" name="celular"
                                value="{{ old('celular', $congregacion->celular) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Distrito <span class="text-danger">*</span></label>
                            <select class="form-select @error('distrito') is-invalid @enderror" name="distrito" required>
                                <option value="" disabled {{ old('distrito', $congregacion->distrito) ? '' : 'selected' }}>Seleccione un distrito...</option>

                                @foreach($distritos as $distrito)
                                    <option value="{{ $distrito->COD_DIST }}" {{ old('distrito', $congregacion->distrito) == $distrito->COD_DIST ? 'selected' : '' }}>
                                        {{ $distrito->NOM_DIST }}
                                    </option>
                                @endforeach
                            </select>

                            @error('distrito')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Apertura</label>
                            <input type="date" class="form-control" name="apertura"
                                value="{{ old('apertura', $congregacion->apertura) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Cierre</label>
                            <input type="date" class="form-control" name="cierre"
                                value="{{ old('cierre', $congregacion->cierre) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observacion" rows="3" required>{{ old('observacion', $congregacion->observacion) }}</textarea>
                    </div>

                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-warning" type="submit">
                            <i class="feather-save me-2"></i>
                            <span>Actualizar Congregación</span>
                        </button>
                        <a href="{{ route('maestras.congregacion.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>

                <script>
                    (function () {
                        'use strict'
                        const form = document.getElementById('formUpdateCongregacion');
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    })();
                </script>

                {{-- Buscar Nombre del Pastor --}}
                <script>
                    function buscarPastorPorCampo(cedulaInputId, nombreInputId) {
                        const cedulaInput = document.getElementById(cedulaInputId);
                        const nombreInput = document.getElementById(nombreInputId);

                        function buscar(cedula) {
                            if (cedula.length >= 5) {
                                nombreInput.value = 'Buscando...';
                                fetch(`/buscar-pastor?cedula=${cedula}`)
                                    .then(res => {
                                        if (!res.ok) throw new Error();
                                        return res.json();
                                    })
                                    .then(data => {
                                        nombreInput.value = data.nombre;
                                    })
                                    .catch(() => {
                                        nombreInput.value = '❌ No encontrado';
                                    });
                            } else {
                                nombreInput.value = '';
                            }
                        }

                        cedulaInput.addEventListener('input', () => buscar(cedulaInput.value));
                        cedulaInput.addEventListener('paste', () => {
                            setTimeout(() => buscar(cedulaInput.value), 10);
                        });

                        // Ejecutar al cargar si hay valor
                        document.addEventListener('DOMContentLoaded', () => {
                            if (cedulaInput.value.length >= 5) {
                                buscar(cedulaInput.value);
                            }
                        });
                    }

                    // Activar búsqueda en ambos campos
                    buscarPastorPorCampo('cedulaPastor', 'nombrePastor');
                    buscarPastorPorCampo('cedulaPastorAnterior', 'nombrePastorAnterior');
                </script>



                </div>
            </div>
        </div>
    </div>
</x-base-layout>