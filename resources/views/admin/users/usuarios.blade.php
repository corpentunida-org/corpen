<x-base-layout>
    @section('titlepage', 'Usuarios')
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="fw-bold">Actividad Reciente:</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="col-sm-12">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Area</th>
                                    <th>Actividad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($registros as $i => $r)
                                    <tr>
                                        <td>
                                        <span class="wd-10 ht-10 bg-warning me-2 d-inline-block rounded-circle"></span>
                                            {{ $r->fechaRegistro }} {{ $r->horaRegistro }}
                                        </td>
                                        <td>
                                            {{ strtoupper($r->usuario) }}
                                        </td>
                                        <td>
                                            @if ( $r->area == 'EXEQUIALES')
                                                <span class="badge bg-soft-primary text-primary">
                                            @elseif ( $r->area == 'SEGUROS')
                                                <span class="badge bg-soft-success text-success">
                                            @elseif ( $r->area == 'CINCO')
                                                <span class="badge bg-soft-info text-info">
                                            @elseif ( $r->area == 'CREDITOS')
                                                <span class="badge bg-soft-danger text-danger">
                                            @else
                                                <span class="badge bg-soft-danger text-warning">
                                            @endif
                                            {{ $r->area }} </span>
                                        </td>
                                        <td>
                                            {{ strtoupper($r->accion) }}
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
