<x-base-layout>
    @section('titlepage', 'Dashboard reclamaciones')
    
    <div class="col-10">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="hstack justify-content-between mb-4 pb-">
                    <div>
                        <h5 class="mb-1">Estadisticas Beneficiarios</h5>
                        <span class="fs-12 text-muted">Sobre el total de acciones en la app Exequiales</span>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-xxl-3 col-md-6">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Beneficiarios Agregados</h6>
                                </div>
                                <canvas id="kpiChart-1" style="width:70px;height: 70px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Beneficiarios Actualizados</h6>
                                </div>
                                <canvas id="kpiChart-2" style="width:70px;height: 70px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Beneficiarios Eliminados</h6>
                                </div>
                                <canvas id="kpiChart-3" style="width:70px;height: 70px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="card-body border border-dashed border-gray-5 rounded-3 position-relative">
                            <div class="hstack justify-content-between gap-4">
                                <div>
                                    <h6 class="fs-14 text-truncate-1-line">Servicios Prestados</h6>
                                </div>
                                <canvas id="kpiChart-4" style="width:70px;height: 70px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-2">
        <div class="d-grid">
            <div class="card border border-dashed border-gray-5">
                <div class="card-body rounded-3 text-center">
                    <div class="fs-4 fw-bolder text-dark">{{number_format($numtotaltitulares)}}</div>
                    <p class="fw-medium text-muted">
                        Total Asociados
                    </p>
                </div>
            </div>
            <div class="card border border-dashed border-gray-5">
                <div class="card-body rounded-3 text-center">
                    <div class="fs-4 fw-bolder text-dark">{{number_format($numtotalbeneficiarios)}}</div>
                    <p class="fw-medium text-muted">
                        Total Beneficiarios
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Servicios prestados:</span>
                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                        Servicios prestados segmentados por distrito del titular.</span>
                </h5>
            </div>
            <div class="card-body">
                <canvas id="workloadChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Servicios prestados:</span>
                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                        Servicios prestados segmentados por municipio del lugar de fallecimiento.</span>
                </h5>
            </div>
            <div class="card-body">
                <canvas id="workloadCharttwo"></canvas>
            </div>
        </div>
    </div>


    <script>
        const dynamicHeight = Math.max(300, @json($arraydata['labels']).length * 30);
        const canvas = document.getElementById('workloadChart');
        const ctxone = canvas.getContext('2d');
        canvas.height = dynamicHeight;
        new Chart(ctxone, {
            type: 'bar',
            data: {
                labels: @json($arraydata['labels']),
                datasets: [{
                    data: @json($arraydata['valores']),
                    backgroundColor: '#6C4CF2',
                    borderRadius: 8,
                    barThickness: 18
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 20,
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.raw} servicios`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                }
            }
        });

        canvastwo = document.getElementById('workloadCharttwo');
        const ctxtwo = canvastwo.getContext('2d');
        new Chart(ctxtwo, {
            type: 'bar',
            data: {
                labels: @json($arraydatamunicipios['labels']),
                datasets: [{
                    data: @json($arraydatamunicipios['valores']),
                    backgroundColor: '#3454D1',
                    borderRadius: 8,
                    barThickness: 18
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 20,
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.raw} servicios`
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                }
            }
        });
        const kpis = @json($kpis);
        console.log(kpis);
        kpis.forEach((kpi, index) => {
            const ctx = document
                .getElementById(`kpiChart-${index+1}`)
                .getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [kpi.percent, 100 - kpi.percent],
                        backgroundColor: [
                            kpi.color,
                            '#E5E7EB'
                        ],
                        borderWidth: 0,
                        borderRadius: 8,
                        spacing: 0,
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: false,
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
                        ctx.font = 'bold 18px Arial';
                        ctx.fillStyle = '#111827';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(
                            kpi.percent + '%',
                            width / 2,
                            height / 2
                        );
                        ctx.save();
                    }
                }]
            });
        });
    </script>
</x-base-layout>
