<div class="payment-history">
    <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0">Beneficiarios:</h5>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr class="border-top">
                    <th>Tipo Documento</th>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Parentesco</th>
                    <th>Porcentaje</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beneficiarios as $b)
                    <tr>
                        <td>{{ $b->tipo_documento_id }}</td>
                        <td><a href="javascript:void(0);">{{ $b->cedula }}</a></td>
                        <td>{{ $b->nombre }}</td>
                        <td>{{ $b->parentescos->name }}</td>
                        <td><span class="badge bg-soft-warning text-warning">{{ $b->porcentaje }}%</span></td>
                        <td class="hstack justify-content-end gap-4 text-end">
                            <div class="dropdown">
                                <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown"
                                    data-bs-offset="0,21">
                                    <i class="feather feather-more-horizontal"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('seguros.beneficiario.edit', ['beneficiario' => $b->id]) }}">
                                            <i class="feather feather-edit-3 me-3"></i>
                                            <span>Editar</span>
                                        </a>
                                    </li>                                   
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)">
                                            <i class="feather feather-trash-2 me-3"></i>
                                            <span>Eliminar</span>
                                        </a>
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
