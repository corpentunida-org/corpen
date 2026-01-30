<x-base-layout>
    @section('titlepage', 'Quizes')

    <div class="col-xxl-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Participantes del Quiz</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="projectList">
                    <thead>
                        <tr>
                            <th>Correo</th>
                            <th>Nombre</th>
                            <th>Puntaje</th>
                            <th>TIC</th>
                            <th>Nuevas TIC</th>
                            <th>Tiempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pruebausuarios as $user)
                            <tr>
                                <td>{{ $user->id_correo }}</td>
                                <td>{{ $user->nombre }}</td>
                                <td>{{ $user->puntaje }}</td>
                                <td>{{ $user->respuestas[5] }} <span style="color: #f5c518;">★</span></td>
                                <td>
                                    {{ $user->respuestas[6] }}
                                    {!! $user->respuestas[6] !== 'n/a' ? '<span style="color:#f5c518;">★</span>' : '' !!}
                                </td>
                                <td>{{ $user->tiempo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- [Sales Pipeline] end -->
    <!-- [Revenue Forecast] start -->
    <div class="col-xxl-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Revenue Forecast</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action">
                <div class="text-center mb-4">
                    <canvas id="ChartTotalQuiz"></canvas>
                </div>
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-activity"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Marketing Gaol</h2>
                            <div class="fs-11 text-muted">$550/$1250 USD</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-users"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Teams Goal</h2>
                            <div class="fs-11 text-muted">$550/$1250 USD</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-check-circle"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Leads Goal</h2>
                            <div class="fs-11 text-muted">$850/$950 USD</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-dollar-sign"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Revenue Goal</h2>
                            <div class="fs-11 text-muted">$5,655/$12,500 USD</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="javascript:void(0);" class="btn btn-primary">Generate Report</a>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const porcentaje = 20;
            const ctx = document.getElementById('ChartTotalQuiz').getContext('2d');
        });
    </script>
</x-base-layout>
