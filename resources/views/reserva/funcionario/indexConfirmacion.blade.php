
<x-base-layout>
    @section('titlepage', 'Confirmar reserva Asociado')
    <x-success />

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <x-success />
                    @if($reservas->count() == 0)
                        <div class="alert alert-warning text-center" style="margin-top: 15px">
                            Aún no hay reservas.
                        </div>
                    @else
                        <table class="table table-hover" id="customerList">
                            <thead>
                            <tr>
                                <th>Inmueble</th>
                                <th>Asociado</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th class="text-end">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($reservas as $reserva)
                                <tr>
                                    <td>
                                        <div class="fw-semibold mb-1"></div>
                                        <div class="d-flex gap-3">
                                            <a href="javascript:void(0);"
                                               class="hstack gap-1 fs-11 fw-normal text-primary">
                                                <span>{{ $reserva->res_inmueble->name }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        @if( $reserva->endosada)
                                            <div class="fw-semibold mb-1">{{ $reserva->name_reserva }}</div>
                                        @else
                                            <div class="fw-semibold mb-1">{{ $reserva->user->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $reserva->fecha_inicio }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $reserva->fecha_fin }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold mb-1">{{ $reserva->res_status->name }}</div>
                                    </td>

                                    <td>
                                        <div class="hstack gap-2 justify-content-end">

                                            <a href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-grid"></i>
                                            </a>
                                            <a href="{{ route('reserva.edit', $reserva->id) }}"
                                               class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('reserva.destroy', $reserva->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Está seguro de eliminar esta reserva?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
    </div>

</x-base-layout>
