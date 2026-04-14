<x-base-layout>
    @section('titlepage', 'Dashboard de Reservas')
    <x-success />
    
    <div class="main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-extrabold mb-1 pl-2">Dashboard General</h1>
        <div class="header-actions d-flex">
            {{-- Botón rápido para ir a la lista de inmuebles --}}
            <a href="{{ route('reserva.crudinmuebles.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4 fw-bold me-2">
                <i class="bi bi-building me-1"></i> Ver Inmuebles
            </a>
            {{-- Botón rápido para gestionar reservas (si aplica el permiso) --}}
            @candirect('reservas.Reserva.lista')
            <a href="{{ route('reserva.inmueble.confirmacion') }}" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-calendar-check me-1"></i> Gestionar Reservas
            </a>
            @endcandirect
        </div>
    </div>

    {{-- Tarjetas de Métricas (KPIs) --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 bg-primary text-white h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-buildings fs-1 mb-2"></i>
                    <h5 class="card-title fw-bold">Inmuebles Activos</h5>
                    <h2 class="display-5 fw-extrabold mb-0">{{ $total_inmuebles }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 bg-warning text-dark h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-hourglass-split fs-1 mb-2"></i>
                    <h5 class="card-title fw-bold">Reservas Pendientes</h5>
                    <h2 class="display-5 fw-extrabold mb-0">{{ $reservas_pendientes }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 bg-success text-white h-100 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-check-circle fs-1 mb-2"></i>
                    <h5 class="card-title fw-bold">Reservas Confirmadas</h5>
                    <h2 class="display-5 fw-extrabold mb-0">{{ $reservas_confirmadas }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Últimas Reservas --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h5 class="fw-bold"><i class="bi bi-clock-history me-2"></i> Últimas Reservas Registradas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Inmueble</th>
                            <th>Solicitante</th>
                            <th>Fechas (Inicio - Fin)</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimas_reservas as $reserva)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $reserva->res_inmueble->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    {{ $reserva->user->name ?? 'Usuario Desconocido' }}<br>
                                    <small class="text-muted">{{ $reserva->celular }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }}
                                    </span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <span class="badge bg-light text-dark border">
                                        {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    {{-- Dependiendo del status_id pintamos un color (Ajusta según tu tabla res_status) --}}
                                    @if($reserva->res_status_id == 1)
                                        <span class="badge bg-warning text-dark">Solicitada</span>
                                    @elseif($reserva->res_status_id == 2)
                                        <span class="badge bg-success">Confirmada</span>
                                    @elseif($reserva->res_status_id == 4)
                                        <span class="badge bg-danger">Cancelada</span>
                                    @elseif($reserva->res_status_id == 5)
                                        <span class="badge bg-info text-dark">Soporte Subido</span>
                                    @else
                                        <span class="badge bg-secondary">Otro</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        Ver Detalles
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No hay reservas registradas recientemente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-base-layout>