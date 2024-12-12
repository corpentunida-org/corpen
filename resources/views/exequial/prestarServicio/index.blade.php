<x-base-layout>
@section('titlepage', 'Prestar Servicio')
    <x-success />
<div class="col-lg-12">
    <div class="card stretch stretch-full">
        <div class="card-body p-0">
            <div class="table-responsive">
                <div id="customerList_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">

                    <div class="col-sm-12">
                        <table class="table table-hover" id="customerList">
                            <thead>
                                <tr>
                                    <th>Registro</th>
                                    <th>Titular</th>
                                    <th>Fallecido</th>
                                    <th>Parentesco</th>
                                    <th>Contacto</th>
                                    <th>Factura</th>
                                    <th>Traslado</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registros as $r)
                                    <tr class="single-item">
                                        <td>
                                            <select class="form-control" data-select2-selector="tag" tabindex="-1">
                                                <option value="primary" data-bg="bg-primary">{{$r->fechaRegistro}}</option>
                                                <option value="primary" data-bg="bg-primary">Martes</option>
                                                <option value="primary" data-bg="bg-primary">{{$r->horaFallecimiento}}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" data-select2-selector="status">
                                                <option value="warning" data-bg="bg-warning">{{$r->cedulaTitular}}</option>
                                                <option value="warning" data-bg="bg-warning">{{$r->nombreTitular}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" data-select2-selector="status">
                                                <option value="primary" data-bg="bg-primary">{{$r->cedulaFallecido}}
                                                </option>
                                                <option value="primary" data-bg="bg-primary">{{$r->nombreFallecido}}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <span class="text-truncate-1-line">
                                                @if ($r->parentesco == null)
                                                    TITULAR
                                                @else
                                                    {{$r->parentesco}}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <select class="form-control" data-select2-selector="status">
                                                <option value="warning" data-bg="bg-warning">{{$r->contacto}}</option>
                                                <option value="warning" data-bg="bg-warning">{{$r->telefonoContacto}}
                                                </option>
                                                <option value="warning" data-bg="bg-warning">{{$r->contacto2}}</option>
                                                <option value="warning" data-bg="bg-warning">{{$r->telefonoContacto2}}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" data-select2-selector="status">
                                                <option value="success" data-bg="bg-success">{{$r->factura}}</option>
                                                <option value="success" data-bg="bg-success">{{$r->valor}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            @if ($r->traslado)
                                                <div class="badge bg-soft-success text-success">Si</div>
                                            @else
                                                <div class="badge bg-soft-danger text-danger">No</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 justify-content-end">
                                                <a href="proposal-view.html" class="avatar-text avatar-md">
                                                    <i class="feather feather-eye"></i>
                                                </a>
                                                <div class="dropdown">
                                                    <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                        data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                        <i class="feather feather-more-horizontal"></i>
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{route('exequial.prestarServicio.edit', $r->id)}}" class="dropdown-item">
                                                                <i class="feather feather-edit-3 me-3"></i>
                                                                <span>Editar</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item printBTN" href="javascript:void(0)">
                                                                <i class="feather feather-printer me-3"></i>
                                                                <span>Imprimir</span>
                                                            </a>
                                                        </li>
                                                        <li class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)">
                                                                <i class="fa-regular fa-bell"></i>
                                                                <span>Prestar Servicio</span>
                                                            </a>
                                                        </li>
                                                        <li class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)">
                                                                <i class="feather feather-trash-2 me-3"></i>
                                                                <span>Eliminar</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
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
    </div>
</div>

</x-base-layout>
