
<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            @foreach($inmuebles as $inmueble)
                <div class="card card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <h5 class="fs-4">{{ $inmueble->name }}</h5>
                            <span class="text-muted">{{ $inmueble->description }}</span>
                        </div>
                        <div class="btn btn-md btn-success">
                            <a href="{{ route('reserva.inmueble.create', $inmueble->id) }}" style="color: white">Reservar</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div class="table-responsive">
                    @if($reservas->count() == 0)
                        <div class="alert alert-warning text-center" style="margin-top: 15px">
                            Aún no hay reservas.
                        </div>
                    @else
                        <table class="table table-hover" id="customerList">
                            <thead>
                            <tr>
                                <th>Fallecimiento</th>
                                <th>Fallecido</th>
                                <th>Parentesco</th>
                                <th>Titular</th>
                                <th>Factura</th>
                                <th>Traslado</th>
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
                                                <i class="feather-clock fs-10 text-primary"></i>
                                                <span></span>
                                            </a>
                                            <a href="javascript:void(0);"
                                               class="hstack gap-1 fs-11 fw-normal text-primary">
                                                <i class="feather-grid calendar-icon fs-10 text-primary"></i>
                                                <span>prueba</span>
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-semibold mb-1">pruyeba</div>
                                        <div class="d-flex gap-3">
                                            <a
                                                href="javascript:void(0);"class="hstack gap-1 fs-11 fw-normal text-muted">prueba</a>
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="fw-semibold mb-1">titular</div>
                                        <div class="d-flex gap-3">
                                            <a
                                                href="javascript:void(0);"class="hstack gap-1 fs-11 fw-normal text-muted">prueba</a>
                                        </div>
                                    </td>
                                    {{-- <td>
                                        <select class="form-control" data-select2-selector="status">
                                            <option value="success" data-bg="bg-success">{{ $r->factura }}
                                            </option>
                                            <option value="success" data-bg="bg-success">{{ $r->valor }}
                                            </option>
                                        </select>
                                    </td> --}}
                                    <td>
                                        <div>
                                            <a href="javascript:void(0);"
                                               class="fs-12 fw-normal text-muted d-block">001</a>
                                            <a href="javascript:void(0);" class="d-block">$ 12</a>
                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">



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
