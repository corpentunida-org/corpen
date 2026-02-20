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
                            <div class="alert alert-dismissible p-2 d-flex alert-soft-danger-message" role="alert">
                                <div class="p-2">
                                    <p class="fw-bold text-truncate-1-line">IMPORTANTE!</p>
                                    <p class="fs-12 fw-medium">Las reservas que se encuentren en estado
                                        <strong>REVISIÓN </strong>deberán adjuntar el soporte de pago mediante el botón
                                        verde dentro de los TRES (3) DÍAS siguientes a la fecha de creación de la
                                        reserva.<strong>
                                            EN CASO DE NO ANEXAR el comprobante dentro de este plazo, </strong> la
                                        reserva será
                                        cancelada automáticamente y el espacio será asignado a otro asociado.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Inmueble</th>
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
                                                <a href="{{ route('reserva.inmueble.soporte.create', $reserva->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-cloud-upload me-2"></i> Anexar comprobante
                                                </a>
                                                {{-- <a href="{{ route('reserva.reserva.show', $reserva->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-grid"></i>
                                                </a> --}}
                                                <form action="{{ route('reserva.reserva.destroy', $reserva->id) }}"
                                                    method="POST">
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
