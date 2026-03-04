
<x-base-layout>
    @section('titlepage', 'Confirmar Reserva Asociado')
    <x-success />

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div class="table-responsive">                    
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
                                            <span class="text-muted">{{ $reserva->nid }}</span>
                                            <div class="fw-semibold mb-1">{{ $reserva->user->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                       {{ $reserva->fecha_inicio }}
                                    </td>
                                    <td>
                                        {{ $reserva->fecha_fin }}
                                    </td>
                                    <td>                                        
                                        <div class="btn btn-sm bg-soft-teal text-teal d-inline-block">{{ $reserva->res_status->name }}</div>
                                    </td>
                                    <td>
                                        <div class="hstack gap-3 justify-content-end">
                                            <a href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}" class="fw-bold text-primary">Agregar Comentario</a>
                                            {{--<a href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-grid"></i>
                                            </a>
                                            <a href="#"  href="{{ route('reserva.reserva.edit', $reserva->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>--}}
                                            <form action="{{ route('reserva.reserva.destroy', $reserva->id) }}" method="POST">
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
                    
                </div>
            </div>

        </div>
    </div>
    </div>

</x-base-layout>
