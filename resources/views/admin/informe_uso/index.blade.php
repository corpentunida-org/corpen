@php
    $formatoDuracion = fn (int $segundos) => \App\Http\Controllers\Admin\InformeUsoController::formatoDuracion($segundos);
@endphp
<x-base-layout>
    @section('titlepage', 'Informe de Uso')

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <form action="{{ route('admin.informeuso.index') }}" method="GET" class="row g-3 align-items-end" id="formFiltros">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $desde }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1">Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $hasta }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small text-muted mb-1 d-block">Rangos rápidos</label>
                        <div class="btn-group btn-group-sm w-100">
                            <button type="button" class="btn btn-outline-secondary" onclick="rangoRapido('mes')">Este mes</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="rangoRapido('anio')">Este año</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="rangoRapido('todo')">Todo</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
                    </div>
                </form>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.informeuso.excel', request()->query()) }}" data-no-loading class="btn btn-sm btn-warning">
                        <i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel
                    </a>
                    <a href="{{ route('admin.informeuso.pdf', request()->query()) }}" data-no-loading class="btn btn-sm btn-primary">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $areaTop['area'] ?? '—' }}</h5>
                        <span class="text-muted">ÁREA CON MÁS USO</span>
                        @if ($areaTop)
                            <span class="fs-11 text-dark badge bg-gray-100">{{ $areaTop['total'] }} acciones</span>
                        @endif
                    </div>
                    <div class="avatar-text avatar-lg bg-success text-white rounded">
                        <i class="feather-pie-chart"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $usuarioTop['nombre'] ?? '—' }}</h5>
                        <span class="text-muted">USUARIO MÁS ACTIVO (ACCIONES)</span>
                        @if ($usuarioTop)
                            <span class="fs-11 text-dark badge bg-gray-100">{{ $usuarioTop['total'] }} acciones</span>
                        @endif
                    </div>
                    <div class="avatar-text avatar-lg bg-primary text-white rounded">
                        <i class="feather-activity"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $tiempoActivoTop['nombre'] ?? 'Sin datos aún' }}</h5>
                        <span class="text-muted">MAYOR TIEMPO ACTIVO</span>
                        @if ($tiempoActivoTop)
                            <span class="fs-11 text-dark badge bg-gray-100">{{ $formatoDuracion($tiempoActivoTop['segundos_total']) }}</span>
                        @endif
                    </div>
                    <div class="avatar-text avatar-lg bg-info text-white rounded">
                        <i class="feather-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $tiempoGestionesTop['nombre'] ?? 'Sin datos' }}</h5>
                        <span class="text-muted">MÁS TIEMPO EN GESTIONES</span>
                        @if ($tiempoGestionesTop)
                            <span class="fs-11 text-dark badge bg-gray-100">{{ $formatoDuracion($tiempoGestionesTop['segundos_total']) }}</span>
                        @endif
                    </div>
                    <div class="avatar-text avatar-lg bg-warning text-white rounded">
                        <i class="feather-phone-call"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Uso por área</h5>
            </div>
            <div class="card-body">
                @forelse ($porArea as $fila)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $fila['area'] }}</span>
                            <span class="text-muted small">{{ $fila['total'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $fila['porcentaje'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">No hay actividad para el periodo seleccionado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Uso por usuario</h5>
            </div>
            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                @forelse ($porUsuario as $fila)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">{{ $fila['nombre'] }}</span>
                            <span class="text-muted small">{{ $fila['total'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $fila['porcentaje'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">No hay actividad para el periodo seleccionado.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">
                    Tiempo activo por usuario
                    <span class="fs-12 fw-normal text-muted">basado en inicio/cierre de sesión, toda la app</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @if ($tiempoActivo->isEmpty())
                    <p class="text-muted text-center py-4 mb-0 px-4">
                        Todavía no hay datos de tiempo activo en este periodo. Esta métrica se empezó a registrar
                        el 14/09/2026 — no hay forma de calcularla retroactivamente para sesiones anteriores a esa fecha.
                    </p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Sesiones</th>
                                    <th>Tiempo activo total</th>
                                    <th>Promedio por sesión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tiempoActivo as $fila)
                                    <tr>
                                        <td class="fw-semibold">{{ $fila['nombre'] }}</td>
                                        <td>{{ $fila['sesiones'] }}</td>
                                        <td>{{ $formatoDuracion($fila['segundos_total']) }}</td>
                                        <td class="text-muted small">{{ $formatoDuracion($fila['segundos_promedio']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">
                    Tiempo en gestiones (Interacciones)
                    <span class="fs-12 fw-normal text-muted">duración de llamadas/gestiones, histórico real</span>
                </h5>
            </div>
            <div class="card-body p-0">
                @if ($tiempoGestiones->isEmpty())
                    <p class="text-muted text-center py-4 mb-0 px-4">No hay gestiones registradas en este periodo.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Agente</th>
                                    <th>Gestiones</th>
                                    <th>Tiempo total</th>
                                    <th>Promedio por gestión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tiempoGestiones as $fila)
                                    <tr>
                                        <td class="fw-semibold">{{ $fila['nombre'] }}</td>
                                        <td>{{ $fila['gestiones'] }}</td>
                                        <td>{{ $formatoDuracion($fila['segundos_total']) }}</td>
                                        <td class="text-muted small">{{ $formatoDuracion($fila['segundos_promedio']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function rangoRapido(tipo) {
                var hoy = new Date();
                var desde;
                if (tipo === 'mes') {
                    desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                } else if (tipo === 'anio') {
                    desde = new Date(hoy.getFullYear(), 0, 1);
                } else {
                    desde = new Date(2020, 0, 1);
                }
                document.getElementById('fecha_desde').value = desde.toISOString().slice(0, 10);
                document.getElementById('fecha_hasta').value = hoy.toISOString().slice(0, 10);
                document.getElementById('formFiltros').submit();
            }
        </script>
    @endpush
</x-base-layout>
