{{-- resources/views/creditos/congregaciones/create.blade.php --}}
<x-base-layout>
    @section('titlepage', 'Registrar Nueva Congregación')

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>¡Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if ($errors->any() && !session('error'))
        <div class="alert alert-danger">
            <strong>¡Atención!</strong> Por favor, corrige los errores marcados en el formulario.
        </div>
    @endif

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Formulario de Registro de Congregación</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('maestras.congregacion.store') }}" method="POST" id="formCreateCongregacion" novalidate>
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-bold align-middle" style="width: 15%;">Código <span class="text-danger">*</span></td>
                                    <td>
                                        <input type="text" class="form-control @error('Codigo') is-invalid @enderror" name="Codigo" placeholder="Ej: C001" required value="{{ old('Codigo') }}" data-bs-toggle="tooltip" title="Código único para identificar la congregación (Ej: C001)">
                                        @error('Codigo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="fw-bold align-middle" style="width: 15%;">Nombre Templo <span class="text-danger">*</span></td>
                                    <td style="width: 35%;">
                                        <input type="text" class="form-control text-uppercase @error('nombre') is-invalid @enderror" name="nombre" placeholder="Nombre completo del templo" required value="{{ old('nombre') }}" data-bs-toggle="tooltip" title="Nombre oficial del templo o congregación">
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold align-middle">Pastor</td>
                                    <td>
                                        <input type="number" class="form-control @error('pastor') is-invalid @enderror" name="pastor" id="pastorInput" placeholder="Cédula del pastor a cargo" value="{{ old('pastor') }}" min="1" data-bs-toggle="tooltip" title="Ingrese la cédula sin puntos ni comas">
                                        @error('pastor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <input type="text" id="pastorNombre" class="form-control mt-2" value="Nombre del pastor..." disabled>
                                    </td>

                                    <td class="fw-bold align-middle">Clase <span class="text-danger">*</span></td>
                                    <td>
                                        <select class="form-select @error('clase') is-invalid @enderror" name="clase" required data-bs-toggle="tooltip" title="Selecciona la clase o tipo de congregación">
                                            <option value="" disabled selected>Seleccione una clase...</option>
                                            @foreach($claselist as $c)
                                                <option value="{{$c->id}}" {{ old('clase') == $c->id ? 'selected' : '' }}>{{$c->nombre}}</option>
                                            @endforeach                                            
                                        </select>
                                        @error('clase')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold align-middle">Municipio</td>
                                    <td>
                                        <select class="form-select @error('municipio') is-invalid @enderror" name="municipio" required data-bs-toggle="tooltip" title="Selecciona el municipio donde se ubica la congregación">
                                            <option value="" disabled selected>Seleccione un municipio...</option>
                                            @foreach($municipios as $m)
                                                <option value="{{ $m->id }}" {{ old('municipio') == $m->id ? 'selected' : '' }}>
                                                    {{ $m->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('municipio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td class="fw-bold align-middle">Dirección</td>
                                    <td>
                                        <input type="text" class="form-control @error('direccion') is-invalid @enderror" name="direccion" placeholder="Dirección completa" value="{{ old('direccion') }}" data-bs-toggle="tooltip" title="Calle, carrera, número, barrio u otra referencia">
                                        @error('direccion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold align-middle">Teléfono</td>
                                    <td>
                                        <input type="tel" class="form-control @error('telefono') is-invalid @enderror" name="telefono" placeholder="Número de teléfono fijo" value="{{ old('telefono') }}" data-bs-toggle="tooltip" title="Número de teléfono fijo con indicativo si aplica">
                                        @error('telefono')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="fw-bold align-middle">Celular</td>
                                    <td>
                                        <input type="tel" class="form-control @error('celular') is-invalid @enderror" name="celular" placeholder="Número de celular de contacto" value="{{ old('celular') }}" data-bs-toggle="tooltip" title="Número de celular con 10 dígitos. Ej: 3111234567">
                                        @error('celular')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold align-middle">Distrito <span class="text-danger">*</span></td>
                                    <td>
                                        <select class="form-select @error('distrito') is-invalid @enderror" name="distrito" required data-bs-toggle="tooltip" title="Selecciona el distrito al que pertenece esta congregación">
                                            <option value="" disabled {{ old('distrito') ? '' : 'selected' }}>Seleccione un distrito...</option>
                                            @foreach($distritos as $distrito)
                                                <option value="{{ $distrito->COD_DIST }}" {{ old('distrito') == $distrito->COD_DIST ? 'selected' : '' }}>
                                                    {{ $distrito->NOM_DIST }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('distrito')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>

                                    <td class="fw-bold align-middle">Estado <span class="text-danger">*</span></td>
                                    <td>
                                        <select class="form-select @error('estado') is-invalid @enderror" name="estado" required>
                                            <option value="1" {{ old('estado', '1') == '1' ? 'selected' : '' }}>Activo</option>
                                            <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold align-middle">Fecha Apertura</td>
                                    <td>
                                        <input type="date" class="form-control @error('apertura') is-invalid @enderror" name="apertura" value="{{ old('apertura') }}" data-bs-toggle="tooltip" title="Fecha en que se fundó o inició actividades la congregación">
                                        @error('apertura')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="fw-bold align-middle">Fecha Cierre</td>
                                    <td>
                                        <input type="date" class="form-control @error('cierre') is-invalid @enderror" name="cierre" value="{{ old('cierre') }}" data-bs-toggle="tooltip" title="Fecha en que dejó de operar, si aplica">
                                        @error('cierre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <td class="fw-bold align-middle">Observaciones</td>
                                    <td colspan="3">
                                        <textarea class="form-control @error('observacion') is-invalid @enderror" name="observacion" rows="3" placeholder="Anotaciones importantes sobre la congregación...">{{ old('observacion') }}</textarea>
                                        @error('observacion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-success" type="submit">
                            <i class="feather-plus me-2"></i>
                            <span>Guardar Congregación</span>
                        </button>
                        <a href="{{ route('maestras.congregacion.index') }}" class="btn btn-light">
                            Cancelar
                        </a>
                    </div>
                </form>

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function () {
                        $('#pastorInput').on('change', function () {
                            const cedula = $(this).val();

                            if (cedula.length > 5) {
                                $.ajax({
                                    url: '{{ route("buscar.pastor") }}',
                                    method: 'GET',
                                    data: { cedula: cedula },
                                    success: function (data) {
                                        $('#pastorNombre').val(data.nombre);
                                    },
                                    error: function () {
                                        $('#pastorNombre').val('No encontrado');
                                    }
                                });
                            } else {
                                $('#pastorNombre').val('');
                            }
                        });
                    });
                </script>

                <script>
                    // Activar tooltips Bootstrap 5
                    document.addEventListener('DOMContentLoaded', function () {
                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    });
                </script>

                <script>
                    (function () {
                        'use strict'
                        const forms = document.querySelectorAll('.needs-validation');
                        Array.from(forms).forEach(form => {
                            form.addEventListener('submit', event => {
                                if (!form.checkValidity()) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                                form.classList.add('was-validated');
                            }, false);
                        });
                    })();
                </script>
            </div>
        </div>
    </div>
</x-base-layout>
