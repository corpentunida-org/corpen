<div class="payment-history mt-3">
    <div class="my-4 px-4 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0">Beneficiarios:</h5>
    </div>
    <div class="table">
        <table class="table mb-0">
            <thead>
                <tr class="border-top">                    
                    <th>Nombre</th>
                    <th>Parentesco</th>
                    <th>Porcentaje</th>
                    <th>Fecha Actualización</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beneficiarios as $b)
                    <tr>                        
                        <td>{{ $b->nombre ?? ' ' }}</td>
                        <td>{{ $b->parentescos->name ?? ' ' }}</td>
                        <td><span class="badge bg-soft-warning text-warning">{{ $b->porcentaje }}%</span></td>
                        <td>{{ $b->updated_at->format('d/m/Y') }}</td>
                        <td class="hstack justify-content-end gap-4 text-end">
                            <div class="dropdown open">
                                <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown"
                                    data-bs-offset="0,21">
                                    <i class="feather feather-more-horizontal"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('seguros.beneficiario.edit', ['beneficiario' => $b->id]) }}">
                                            <i class="feather feather-edit-3 me-3"></i>
                                            <span>Editar</span>
                                        </a>
                                    </li>
                                    <li>                                        
                                        <form action="{{ route('seguros.beneficiario.destroy', ['beneficiario' => $b->id]) }}"method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item btnAbrirModalDestroy" data-text="beneficiario">
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
