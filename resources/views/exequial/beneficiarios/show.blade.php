<div class="col-sm-12">
    <table class="table table-hover dataTable no-footer" id="proposalList" aria-describedby="proposalList_info">
        <thead>
            <tr>
                <th class="text-start sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                    aria-label="Proposal: activate to sort column ascending" style="width: 67.9844px;">
                    Cédula</th>
                <th class="sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                    aria-label="Client: activate to sort column ascending" style="width: 247.75px;">
                    Nombre</th>
                <th class="sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                    aria-label="Amount: activate to sort column ascending" style="width: 94.8281px;">
                    Parentesco</th>
                <th class="sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                    aria-label="Status: activate to sort column ascending" style="width: 50.7812px;">
                    Fecha Nacimiento</th>
                <th class="sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                    aria-label="Status: activate to sort column ascending" style="width: 50.7812px;">
                    Edad</th>
                <th class="text-end sorting" tabindex="0" aria-controls="proposalList" rowspan="1" colspan="1"
                    aria-label="Actions: activate to sort column ascending" style="width: 127.484px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($beneficiarios as $beneficiario)
                    @if ($beneficiario['type'] == 'A')
                            <tr class="single-item odd">
                                <td>{{ $beneficiario['documentId'] }}</td>
                                <td>{{ $beneficiario['names'] }}</td>
                                <td>{{ $beneficiario['relationship'] }}</td>
                                <td>{{ $beneficiario['dateBirthday'] }}</td>
                                @php
                                    $fecNac = new DateTime($beneficiario['dateBirthday']);
                                    $fechaActual = new DateTime();
                                    $diferencia = $fecNac->diff($fechaActual);
                                    $edad = $diferencia->y;
                                @endphp
                                <td>{{ $edad }}</td>
                                <td>
                                    <div class="hstack gap-2 justify-content-end">
                                        <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="offcanvas"
                                            data-bs-target="#proposalSent">
                                            <i class="feather feather-send"></i>
                                        </a>
                                        <a href="proposal-view.html" class="avatar-text avatar-md">
                                            <i class="feather feather-eye"></i>
                                        </a>
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown"
                                                data-bs-offset="0,21">
                                                <i class="feather feather-more-horizontal"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                <form action="{{ route('exequial.beneficiarios.edit', ['beneficiario' => $beneficiario['documentId']]) }}" method="GET">
                                                    <input type="hidden" name="asociadoid" value="{{ $asociado['documentId'] }}">                                                    
                                                    <input type="hidden" name="name" value="{{ $beneficiario['names'] }}">
                                                    <input type="hidden" name="relationship" value="{{ $beneficiario['relationship'] }}">
                                                    <input type="hidden" name="dateBirthday" value="{{ $beneficiario['dateBirthday'] }}">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="feather feather-edit-3 me-3"></i>
                                                        <span>Editar</span>
                                                    </button>
                                                </form>                                                    
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
                    @endif
            @endforeach
        </tbody>
    </table>
</div>