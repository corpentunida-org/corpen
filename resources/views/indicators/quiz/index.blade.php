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
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <canvas id="progressChart" class="mb-4"></canvas>
                            <h2 class="fs-13 tx-spacing-1">Usuarios calificados</h2>
                            <div class="fs-11 text-muted">Puntaje aprobado:<span
                                    class="fs-13 fw-bold">{{ $datacharts['usuariosmaspuntaje'] }}</span></div>
                            <div class="fs-11 text-muted">Total empleados: <span
                                    class="fs-13 fw-bold">{{ $datacharts['totalempleados'] }}</span></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <canvas id="chartSatisfaccion"></canvas>
                            @foreach ($datacharts['ticcorpen'] as $puntaje => $cantidad)
                                <div class="fs-11 text-muted">
                                    Puntaje {{ $puntaje }}:
                                    <span class="fs-11 fw-bold">{{ $cantidad }}</span> Registros
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <canvas id="chartSatisfaccionSoft"></canvas>
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
                <a class="btn btn-primary">Generate Report</a>
            </div>
        </div>
    </div>
    <script>
        const porcentaje = {{ round(($datacharts['usuariosmaspuntaje'] / $datacharts['totalempleados']) * 100, 1) }};
        const ctx = document.getElementById('progressChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [porcentaje, 100 - porcentaje],
                    backgroundColor: [
                        '#3454d1',
                        '#e6e6e6'
                    ],
                    borderWidth: 0,
                    borderRadius: 15,
                }]
            },
            options: {
                cutout: '72%',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const {
                        width
                    } = chart;
                    const {
                        height
                    } = chart;
                    const ctx = chart.ctx;
                    ctx.restore();
                    ctx.font = 'bold 30px Arial';
                    ctx.fillStyle = '#111827';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(
                        porcentaje + '%',
                        width / 2,
                        height / 2
                    );
                    ctx.save();
                }
            }]
        });

        const ctxbarras = document.getElementById('chartSatisfaccion').getContext('2d');
        const ctxsoftware = document.getElementById('chartSatisfaccionSoft').getContext('2d');
        labels = ['1', '2', '3', '4', '5'];
        ticCorpen = @json($datacharts['ticcorpen']);
        dataCorpen = labels.map(n => ticCorpen[n] ?? 0)
        ticsoft = @json($datacharts['ticsoft']);
        dataSoft = labels.map(n => ticsoft[n] ?? 0)
        const colors = ['#ef4444', '#f97316', '#22c55e', '#6366f1', '#eab308', '#9ca3af'];
        new Chart(ctxbarras, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataCorpen,
                    backgroundColor: colors,
                    borderWidth: 2,
                    cutout: '65%'
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `Puntaje: ${ctx.label}, Respuestas: ${ctx.raw} personas`
                        }
                    },
                    legend: {
                        display: false
                    },
                }
            }
        });

        new Chart(ctxsoftware, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataSoft,
                    backgroundColor: colors,
                    borderWidth: 2,
                    cutout: '65%'
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx => `Puntaje: ${ctx.label}, Respuestas: ${ctx.raw} personas`
                        }
                    },
                    legend: {
                        display: false
                    },
                }
            }
        });
    </script>

</x-base-layout>
