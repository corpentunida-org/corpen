<x-base-layout>
    @section('titlepage', 'Integraciones')

    <!-- Componentes de alertas (Asegúrate de que soporten session('success') y session('error')) -->
    <x-success />
    <x-error />

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3 mb-4"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm rounded-3 mb-4"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
    @endif

    <!-- TARJETA DE RESUMEN (HEADER) -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm stretch stretch-full short-info-card rounded-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">

                    <!-- Info Principal -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-3 shadow-sm icon">
                            <i class="bi bi-hdd-network fs-3"></i>
                        </div>
                        <div>
                            <h2 class="fs-4 fw-bold text-dark mb-1">Panel de Integraciones</h2>
                            @if($logs->count() > 0)
                                <span class="text-muted fs-13">
                                    <i class="bi bi-clock-history me-1"></i> Última conexión:
                                    <span class="fw-semibold text-dark">{{ $logs->first()->created_at->diffForHumans() }}</span>
                                </span>
                            @else
                                <span class="text-muted fs-13"><i class="bi bi-info-circle me-1"></i> Sin registros previos</span>
                            @endif
                        </div>
                    </div>

                    <!-- Acción -->
                    <form method="POST" action="{{ route('integraciones.test.pastors') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-medium shadow-sm rounded-pill d-flex align-items-center gap-2 transition-all">
                            <i class="bi bi-arrow-repeat fs-5"></i>
                            <span>Probar Conexión Pastors</span>
                        </button>
                    </form>
                </div>

                <!-- Progreso Dinámico: Tasa de Éxito -->
                <div class="bg-light p-3 rounded-3 mt-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fs-13 fw-semibold text-muted text-uppercase tracking-wide">Tasa de Éxito (Últimas peticiones)</span>

                        @php
                            $totalLogs = $logs->count();
                            $exitosos = $logs->where('estado', 'Exitoso')->count();
                            $porcentajeExito = $totalLogs > 0 ? ($exitosos / $totalLogs) * 100 : 0;

                            $colorClass = $porcentajeExito >= 80 ? 'bg-success' : ($porcentajeExito >= 50 ? 'bg-warning' : 'bg-danger');
                            $textColor = $porcentajeExito >= 80 ? 'text-success' : ($porcentajeExito >= 50 ? 'text-warning' : 'text-danger');
                        @endphp

                        <div class="text-end">
                            <span class="fs-5 fw-bold {{ $textColor }}">{{ number_format($porcentajeExito, 0) }}%</span>
                        </div>
                    </div>
                    <div class="progress ht-5 rounded-pill bg-gray-200">
                        <div class="progress-bar {{ $colorClass }} rounded-pill transition-all"
                             role="progressbar"
                             style="width: {{ $porcentajeExito }}%"
                             aria-valuenow="{{ $porcentajeExito }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TARJETA 1: APIS CONFIGURADAS -->
    <div class="card border-0 shadow-sm stretch stretch-full function-table mb-4 rounded-4">
        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-diagram-3-fill text-muted fs-5"></i>
            <h4 class="mb-0 fw-bold text-dark fs-5">Endpoints Configurados</h4>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive px-4 pb-4">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="table-light text-muted fs-12 text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-3 py-3 rounded-start">API</th>
                            <th class="py-3">URL Base</th>
                            <th class="py-3">Estado Actual</th>
                            <th class="text-end pe-3 py-3 rounded-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <tr class="border-bottom">
                            <td class="ps-3 fw-bold text-dark">API Pastors (Producción)</td>
                            <td class="text-muted fs-13">{{ env('API_PRODUCCION') }}/api/Pastors</td>
                            <td>
                                @if($estadoPastors == 'Conectado')
                                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-2 fw-medium">
                                        <i class="bi bi-circle-fill fs-12 me-1"></i> Online
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger rounded-pill px-3 py-2 fw-medium">
                                        <i class="bi bi-x-circle-fill fs-12 me-1"></i> {{ $estadoPastors }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <form method="POST" action="{{ route('integraciones.test.pastors') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light text-primary avatar-text avatar-md rounded-circle" data-bs-toggle="tooltip" title="Forzar Prueba">
                                        <i class="feather-play"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TARJETA 2: HISTORIAL DE LOGS -->
    <div class="card border-0 shadow-sm stretch stretch-full function-table mb-4 rounded-4">
        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4 d-flex align-items-center gap-2">
            <i class="bi bi-list-check text-muted fs-5"></i>
            <h4 class="mb-0 fw-bold text-dark fs-5">Historial de Peticiones (Logs)</h4>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive px-4 pb-4">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="table-light text-muted fs-12 text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-3 py-3 rounded-start">Fecha</th>
                            <th class="py-3">API</th>
                            <th class="py-3">Método / Cód.</th>
                            <th class="py-3">Tiempo (ms)</th>
                            <th class="py-3">Estado</th>
                            <th class="text-end pe-3 py-3 rounded-end">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($logs as $log)
                            <tr class="border-bottom">
                                <td class="ps-3 text-muted fw-medium fs-13">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="fw-semibold text-dark">{{ $log->nombre_api }}</td>
                                <td>
                                    <span class="text-muted fw-bold">{{ $log->metodo }}</span>
                                    <span class="mx-1">/</span>
                                    <span class="fw-bold {{ $log->codigo_respuesta >= 200 && $log->codigo_respuesta < 300 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->codigo_respuesta ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-inline-block fw-bold {{ $log->tiempo_respuesta_ms > 2000 ? 'text-warning' : 'text-success' }}">
                                        {{ $log->tiempo_respuesta_ms }} ms
                                        <i class="bi {{ $log->tiempo_respuesta_ms > 2000 ? 'bi-activity' : 'bi-lightning-charge' }}"></i>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeLog = match($log->estado) {
                                            'Exitoso' => 'bg-soft-success text-success',
                                            'Error' => 'bg-soft-warning text-warning',
                                            default => 'bg-soft-danger text-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeLog }} rounded-pill px-3 py-2 fw-medium">
                                        {{ $log->estado }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    @if($log->mensaje_error)
                                        <button type="button"
                                                class="btn btn-sm btn-light text-danger avatar-text avatar-md rounded-circle"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalError"
                                                onclick="document.getElementById('error_content').innerText = {{ json_encode($log->mensaje_error) }}"
                                                title="Ver Error">
                                            <i class="feather-alert-circle"></i>
                                        </button>
                                    @else
                                        <span class="text-muted fst-italic fs-12">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="avatar-text avatar-xl bg-soft-secondary text-secondary rounded-circle mb-3 mx-auto">
                                        <i class="bi bi-inbox fs-1"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">No hay registros</h5>
                                    <p class="text-muted">Aún no se han ejecutado pruebas de integración.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DE DETALLE DE ERROR -->
    <div class="modal fade" id="modalError" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-bug-fill me-2"></i>Detalles del Error
                    </h5>
                    <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-3 pb-4">
                    <div class="mb-2">
                        <label class="form-label text-muted fw-medium fs-13">Respuesta del Servidor / Excepción</label>
                        <div class="bg-dark text-light p-3 rounded-3" style="max-height: 300px; overflow-y: auto;">
                            <code id="error_content" class="text-light text-wrap" style="word-break: break-all;">
                                <!-- El contenido se llena mediante Javascript al hacer clic -->
                            </code>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</x-base-layout>
