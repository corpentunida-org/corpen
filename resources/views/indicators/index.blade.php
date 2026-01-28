<x-base-layout>
    @section('titlepage', 'Indicadores')
    <div class="col-lg-12">
        <div class="card stretch stretch-full function-table">
            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cálculo</th>
                                <th>Meta</th>
                                <th>Frecuencia</th>
                                <th>Indicador</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($indicators as $ind)
                                <tr>
                                    <td>{{$ind->id}}</td>
                                    <td>{{ $ind->nombre }}</td>
                                    <td>{{ $ind->calculo }}</td>
                                    <td class="fw-bold text-dark">{{ $ind->meta }}</td>
                                    <td>
                                        @if ($ind->frecuencia == 'Trimestral')
                                            <div class="badge bg-soft-warning text-warning">{{ $ind->frecuencia }}</div>
                                        @elseif($ind->frecuencia == 'Semestral')
                                            <div class="badge bg-soft-info text-info">{{ $ind->frecuencia }}</div>
                                        @elseif($ind->frecuencia == 'Mensual')
                                            <div class="badge bg-soft-primary text-primary">{{ $ind->frecuencia }}</div>
                                        @else
                                            <div class="badge bg-soft-success text-success">{{ $ind->frecuencia }}</div>
                                        @endif
                                    </td>
                                    <td>{{ number_format($ind->indicador_calculado, 1) }} % </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <div class="dropdown">
                                                <a class="avatar-text avatar-md ms-auto" data-bs-toggle="dropdown">
                                                    <i class="feather-more-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modalUpdateCalculo"
                                                        data-id="{{ $ind->id }}" data-nombre="{{ $ind->nombre }}"
                                                        data-meta="{{ $ind->meta }}" class="dropdown-item">Agregar
                                                        Cálculo Indicador</a>
                                                </div>
                                            </div>
                                            <a href="#" class="avatar-text avatar-md" data-bs-toggle="tooltip"
                                                title="" data-bs-original-title="Ver detalle">
                                                <i class="feather-arrow-right"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCalculo" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Actualizar Indicador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="ind_id">

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="ind_nombre" disabled>
                        </div>
                    

                        <div class="mb-3">
                            <label class="form-label">Meta</label>
                            <input type="text" class="form-control" name="meta" id="ind_meta">
                        </div>
                

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-base-layout>
