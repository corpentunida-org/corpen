<x-base-layout>
    @section('titlepage', 'Reservas lista')
    <x-success />
    <div class="scrollbar-container ps ps--active-x ps--active-y row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <button class="card-header border-0 text-start w-100 mb-0" data-bs-toggle="collapse" data-bs-target="#resactivas_collapse_0">
                    <div class="mb-0">
                        <h5 class="fw-bold mb-1">Listado de Reserva</h5>
                        <p class="text-muted mb-0 small">Listado detallado de reservas activas</p>
                    </div>
                </button>
                <hr class="m-0">
                <div class="card-body p-0 collapse show mt-0" id="resactivas_collapse_0">
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
                                @foreach ($reservasact as $reserva)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold mb-1"></div>
                                            <div class="d-flex gap-3">
                                                <a class="hstack gap-1 fs-11 fw-normal text-primary">
                                                    <span>{{ $reserva->res_inmueble->name }}</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $reserva->nid }}</div>
                                            {{ $reserva->tercero?->nom_ter ?? $reserva->user->name }}
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
                                                <a {{-- href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}" --}} class="btn btn-sm btn-primary">
                                                    <i class="bi bi-grid"></i>
                                                </a>
                                                <a href="#" {{-- href="{{ route('reserva.reserva.edit', $reserva->id) }}" --}} class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
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

        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <button class="card-header border-0 text-start w-100 mb-0" data-bs-toggle="collapse" data-bs-target="#calendar_collapse_0">
                    <div class="mb-0">
                        <h5 class="mb-0">Reservas Calendario</h5>
                        <p class="text-muted mb-0 small">Calendario detallado de reservas activas.</p>
                    </div>
                </button>
                <hr class="m-0">
                <div class="card-body p-0 collapse show mt-0" id="calendar_collapse_0">

                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <button class="card-header border-0 text-start w-100 mb-0" data-bs-toggle="collapse" data-bs-target="#historicores_collapse_0">
                    <div class="mb-0">
                        <h5 class="fw-bold mb-1">Histórico de Reservas</h5>
                        <p class="text-muted mb-0 small">Listado detallado de todas las reservas registradas.</p>
                    </div>
                </button>
                <hr class="m-0">
                <div class="card-body p-0 collapse show mt-0" id="historicores_collapse_0">
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
                                @foreach ($historicosres as $hisres)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold mb-1"></div>
                                            <div class="d-flex gap-3">
                                                <a class="hstack gap-1 fs-11 fw-normal text-primary">
                                                    <span>{{ $hisres->res_inmueble->name }}</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $hisres->nid }}</div>
                                            {{ $hisres->tercero?->nom_ter ?? $hisres->user->name }}
                                        </td>
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $hisres->fecha_inicio }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $hisres->fecha_fin }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $hisres->res_status->name }}</div>
                                        </td>

                                        <td>
                                            <div class="hstack gap-2 justify-content-end">
                                                <a {{-- href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}" --}} class="btn btn-sm btn-primary">
                                                    <i class="bi bi-grid"></i>
                                                </a>
                                                <a href="#" {{-- href="{{ route('reserva.reserva.edit', $reserva->id) }}" --}} class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
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

    </div>
</x-base-layout>
