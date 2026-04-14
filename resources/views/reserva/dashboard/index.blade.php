<x-base-layout>
    @section('titlepage', 'Dashboard de Reservas')
    
    {{-- Importamos Chart.js para el gráfico de torta --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-success />
    
    <div class="main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-extrabold mb-1 pl-2">Dashboard de Operaciones</h1>
        
        {{-- Botones de Acción --}}
        <div class="header-actions d-flex">
            {{-- Botón para Descargar PDF --}}
            <a href="{{ route('reserva.dashboard.pdf') }}" class="btn btn-danger shadow-sm rounded-pill px-4 fw-bold me-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
            </a>

            {{-- Botón para Generar Informe (CSV) --}}
            <a href="{{ route('reserva.dashboard.exportar') }}" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold me-2">
                <i class="bi bi-file-earmark-excel me-1"></i> Generar Informe
            </a>

            {{-- Botón rápido para ir a la lista de inmuebles --}}
            @candirect('reservas.Inmueble.create')
            <a href="{{ route('reserva.crudinmuebles.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4 fw-bold me-2">
                <i class="bi bi-building me-1"></i> Ver Inmuebles
            </a>
            @endcandirect

            {{-- Botón rápido para gestionar reservas (si aplica el permiso) --}}
            @candirect('reservas.Reserva.lista')
            <a href="{{ route('reserva.inmueble.confirmacion') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-calendar-check me-1"></i> Gestionar Reservas
            </a>
            @endcandirect
        </div>
    </div>

    {{-- Tarjetas de Métricas Rápidas --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-buildings fs-1 mb-2"></i>
                    <h6 class="card-title fw-bold">Inmuebles Activos</h6>
                    <h2 class="display-6 fw-extrabold mb-0">{{ $total_inmuebles }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-warning text-dark h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-hourglass-split fs-1 mb-2"></i>
                    <h6 class="card-title fw-bold">Pendientes / Soportes</h6>
                    <h2 class="display-6 fw-extrabold mb-0">{{ $reservas_pendientes }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-check-circle fs-1 mb-2"></i>
                    <h6 class="card-title fw-bold">Confirmadas</h6>
                    <h2 class="display-6 fw-extrabold mb-0">{{ $reservas_confirmadas }}</h2>
                </div>
            </div>
        </div>
        
        {{-- NUEVA TARJETA: Día con más reservas --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 bg-info text-white h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="bi bi-calendar-star fs-1 mb-2"></i>
                    <h6 class="card-title fw-bold">Día con más reservas</h6>
                    @if($diaMasReservas)
                        <h4 class="fw-extrabold mb-0">{{ \Carbon\Carbon::parse($diaMasReservas->dia)->format('d M Y') }}</h4>
                        <small class="fw-bold">{{ $diaMasReservas->total }} reservas registradas</small>
                    @else
                        <h4 class="fw-extrabold mb-0">N/A</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- NUEVO INFORME: Gráfico de Torta (Estados) --}}
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                    <h5 class="fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i> Reservas por Estado</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 100%; max-width: 300px;">
                        <canvas id="reservasStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- NUEVO INFORME: Canceladas Últimamente --}}
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold text-danger"><i class="bi bi-x-octagon-fill me-2"></i> Canceladas Últimamente</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Inmueble</th>
                                    <th>Asociado</th>
                                    <th>Fecha Cancelación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($canceladasRecientes as $cancelada)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $cancelada->res_inmueble->name ?? 'N/A' }}</td>
                                        <td>{{ $cancelada->user->name ?? 'Desconocido' }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ \Carbon\Carbon::parse($cancelada->updated_at)->diffForHumans() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No hay cancelaciones recientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para renderizar el gráfico de torta con Chart.js --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('reservasStatusChart').getContext('2d');
            
            // Recibimos los datos desde el controlador usando directivas de Blade
            const labels = {!! json_encode($chartLabels) !!};
            const data = {!! json_encode($chartData) !!};

            new Chart(ctx, {
                type: 'doughnut', // 'pie' o 'doughnut' (torta con hueco)
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#ffc107', // Amarillo (Solicitadas)
                            '#28a745', // Verde (Confirmadas)
                            '#dc3545', // Rojo (Canceladas)
                            '#17a2b8', // Info (Con soporte)
                            '#6c757d'  // Gris (Otros)
                        ],
                        borderWidth: 1,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
</x-base-layout>