{{-- resources/views/creditos/congregaciones/create.blade.php --}}
<x-base-layout>
    @section('titlepage', 'Registrar Nueva Congregación')

    {{-- Bloque para mostrar errores si los hay desde el controlador --}}
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                                {{-- Fila para Código y Nombre --}}
                                <tr>
                                    <td class="fw-bold align-middle" style="width: 15%;">Código <span class="text-danger">*</span></td>
                                    <td>
                                        <input type="text" class="form-control" name="Codigo" placeholder="Ej: C001" required>
                                    </td>
                                    <td class="fw-bold align-middle" style="width: 15%;">Nombre Templo <span class="text-danger">*</span></td>
                                    <td style="width: 35%;">
                                        <input type="text" class="form-control text-uppercase" name="Nombre_templo" placeholder="Nombre completo del templo" required>
                                    </td>
                                </tr>
                                
                                {{-- Fila para Pastor, Clase y Estado --}}
                                <tr>
                                    <td class="fw-bold align-middle">Pastor</td>
                                    <td>
                                        <input type="text" class="form-control text-uppercase" name="Pastor" placeholder="Nombre del pastor a cargo">
                                    </td>
                                    {{-- ===== INICIO DE LA MODIFICACIÓN #2 ===== --}}
                                    <td class="fw-bold align-middle">Clase <span class="text-danger">*</span></td>
                                    <td>
                                        <select class="form-select" name="Clase" required>
                                            @foreach($claselist as $c)
                                            <option value="{{$c->id}}">{{$c->nombre}}</option>
                                            @endforeach                                            
                                        </select>
                                    </td>
                                    {{-- ===== FIN DE LA MODIFICACIÓN #2 ===== --}}
                                </tr>

                                {{-- Fila para Municipio y Dirección --}}
                                <tr>
                                    <td class="fw-bold align-middle">Municipio</td>
                                    <td>
                                        <input type="text" class="form-control" name="Municipio" placeholder="Ciudad o municipio">
                                    </td>
                                    <td class="fw-bold align-middle">Dirección</td>
                                    <td>
                                        <input type="text" class="form-control" name="Direccion" placeholder="Dirección completa">
                                    </td>
                                </tr>
                                
                                {{-- Fila para Teléfono y Celular --}}
                                <tr>
                                    <td class="fw-bold align-middle">Teléfono</td>
                                    <td>
                                        <input type="tel" class="form-control" name="Telefono" placeholder="Número de teléfono fijo">
                                    </td>
                                    <td class="fw-bold align-middle">Celular</td>
                                    <td>
                                        <input type="tel" class="form-control" name="Celular" placeholder="Número de celular de contacto">
                                    </td>
                                </tr>
                                
                                {{-- Fila para Distrito y Estado --}}
                                <tr>
                                    <td class="fw-bold align-middle">Distrito <span class="text-danger">*</span></td>
                                    <td>
                                        <select class="form-select" name="Dist" required>
                                            <option value="" selected disabled>Seleccione un distrito...</option>
                                            @for ($i = 1; $i <= 35; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                            <option value="Misionero">Misionero</option>
                                            <option value="Otros">Otros</option>
                                        </select>
                                    </td>
                                    <td class="fw-bold align-middle">Estado <span class="text-danger">*</span></td>
                                    <td>
                                        <select class="form-select" name="Estado" required>
                                            <option value="A" selected>Activo</option>
                                            <option value="I">Inactivo</option>
                                        </select>
                                    </td>
                                </tr>

                                {{-- Fila para Fechas --}}
                                <tr>
                                    <td class="fw-bold align-middle">Fecha Apertura</td>
                                    <td>
                                        <input type="date" class="form-control" name="Fecha_Ap">
                                    </td>
                                    <td class="fw-bold align-middle">Fecha Cierre</td>
                                    <td>
                                        <input type="date" class="form-control" name="Fecha_Cie">
                                    </td>
                                </tr>
                                
                                {{-- Fila para Observaciones --}}
                                <tr>
                                    <td class="fw-bold align-middle">Observaciones</td>
                                    <td colspan="3">
                                        <textarea class="form-control" name="Obser" rows="3" placeholder="Anotaciones importantes sobre la congregación..."></textarea>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    {{-- Botones de Acción --}}
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

                {{-- Script de validación de Bootstrap 5 --}}
                <script>
                    (function () {
                        'use strict'
                        const form = document.getElementById('formCreateCongregacion');
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    })();
                </script>
            </div>
        </div>
    </div>
</x-base-layout>