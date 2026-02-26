<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />
    @if ($reservas->count() != 0)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <div class="hstack justify-content-between">
                        <div>
                            <h2 class="fw-extrabold">Mis Reservas</h2>
                            @if ($reservas->contains('res_status_id', 1))
                                <div class="alert alert-dismissible p-2 d-flex alert-soft-danger-message" role="alert">
                                    <div class="p-2">
                                        <p class="fw-bold text-truncate-1-line">IMPORTANTE!</p>
                                        <p class="fs-12 fw-medium">Las reservas que se encuentren en estado
                                            <strong>RESERVADA </strong>deberán adjuntar el soporte de pago mediante el botón
                                            azul dentro de los TRES (3) DÍAS siguientes a la fecha de creación de la
                                            reserva.<strong>
                                                EN CASO DE NO ANEXAR el comprobante dentro de este plazo, </strong> la
                                            reserva será <strong>
                                            cancelada automáticamente</strong> y el espacio será asignado a otro asociado.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Inmueble</th>
                                    <th>Fecha Inicio - Fin</th>                                    
                                    <th>Comentario</th>
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
                                        <td>{{ $reserva->fecha_inicio }} a {{ $reserva->fecha_fin }}</td>
                                        <td>{{ $reserva->comentario_reserva ?: ' '}}</td>
                                        <td><div class="fw-semibold mb-1">{{ $reserva->res_status->name }}</div></td>
                                        <td>
                                            <div class="hstack gap-2 justify-content-end">
                                            @if($reserva->soporte_pago == null || empty($reserva->soporte_pago))
                                                <a href="{{ route('reserva.inmueble.soporte.create', $reserva->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-cloud-upload me-2"></i> Anexar comprobante
                                                </a>
                                            @else
                                                <a href="{{ $reserva->getFile($reserva->soporte_pago) }}"
                                                    class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="bi bi-paperclip me-2"></i> Ver comprobante
                                                </a>
                                            @endif
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
    @endif

    @include('reserva.asociado.inmueblesindex')
</x-base-layout>
