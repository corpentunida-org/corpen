{{-- resources/views/creditos/congregaciones/index.blade.php --}}

<x-base-layout>
        @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Congregaciones</h5>
                {{-- Botón para ir al formulario de creación --}}
                <a href="{{ route('maestras.congregacion.create') }}" class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Crear Nueva</span>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="border-top">
                            
                            <th>Código</th> 
                            <th>Nombre del templo</th>
                            <th>Estado congregacion</th>
                            <th>Clase congregacion</th> 
                            <th>Municipio</th>
                            <th>Cedula Pastor

                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Suponiendo que pasas una variable $congregaciones desde el controlador --}}
                        @foreach ($congregacion as $congregacion)
                            <tr>
                                
                                <td><a href="javascript:void(0);">{{ $congregacion->codigo }}</a></td>
                                <td>{{ $congregacion->nombre }}</td>
                                <td>
                                    @if ($congregacion->estado) {{-- Asumiendo 'A' para Activo --}}
                                        <span class="badge bg-soft-success text-success">Activo</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>{{ $congregacion->claseCongregacion->nombre ?? ''}}</td>
                                <td>{{ $congregacion->municipio }}</td>
                                <td>{{ $congregacion->pastor }}</td>

                                <td class="hstack justify-content-end gap-4 text-end">
                                    <div class="dropdown open">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-offset="0,21">
                                            <i class="feather feather-more-horizontal"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('maestras.congregacion.edit', $congregacion->codigo) }}">
                                                    <i class="feather feather-edit-3 me-3"></i>
                                                    <span>Editar</span>
                                                </a>
                                            </li>
                                            <li>                                        
                                                <form action="{{ route('maestras.congregacion.destroy', $congregacion->codigo) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item btnEliminar">
                                                        <i class="feather feather-trash-2 me-3"></i>
                                                        <span>Eliminar</span>
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Script para la confirmación de eliminación (opcional pero recomendado) --}}
    <script>
    $(document).ready(function() {
        $('.btnEliminar').click(function(event) {
            event.preventDefault();
            const formToSubmit = $(this).closest('form');
            
            // Asumiendo que tienes un modal con este ID en tu layout base
            $('#ModalConfirmacionEliminar').modal('show'); 
            
            $('#botonSiModal').off('click').on('click', function() {
                formToSubmit.submit();
            });
        });
    });
    </script>
</x-base-layout>