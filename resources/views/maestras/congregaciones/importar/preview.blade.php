<x-base-layout>
    @section('titlepage', 'Revisar Importación')

    <div class="col-lg-12">
        <div class="alert alert-info">
            <strong>Archivo:</strong> {{ $archivoNombre }} — {{ $analisis['total_filas'] }} filas leídas.
            Todavía no se ha guardado nada. Revisa el reporte y confirma abajo.
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ count($analisis['nuevos']) }}</h5>
                <span class="text-muted">CONGREGACIONES NUEVAS</span>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ $analisis['pastor_cambia_count'] }}</h5>
                <span class="text-muted">CAMBIOS DE PASTOR</span>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ $analisis['distrito_cambia_count'] }}</h5>
                <span class="text-muted">CAMBIOS DE DISTRITO</span>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body {{ count($analisis['excepciones']) > 0 ? 'border border-danger' : '' }}">
                <h5 class="fs-4">{{ count($analisis['excepciones']) }}</h5>
                <span class="text-muted">EXCEPCIONES (no se van a aplicar)</span>
            </div>
        </div>
    </div>

    @if (count($analisis['duplicados']) > 0)
        <div class="col-lg-12">
            <div class="alert alert-warning">
                <strong>{{ count($analisis['duplicados']) }} código(s) duplicado(s) dentro del mismo Excel</strong> —
                solo se toma la primera aparición de cada uno: {{ implode(', ', array_slice($analisis['duplicados'], 0, 20)) }}
            </div>
        </div>
    @endif

    @if (count($analisis['excepciones']) > 0)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title text-danger">Excepciones — estas filas NO se van a aplicar</h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analisis['excepciones'] as $exc)
                                <tr>
                                    <td>{{ $exc['codigo'] }}</td>
                                    <td>{{ $exc['nombre'] }}</td>
                                    <td class="text-danger small">{{ implode('; ', $exc['motivos']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if ($analisis['pastor_cambia_count'] > 0)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Cambios de pastor</h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Congregación</th>
                                <th>Pastor anterior</th>
                                <th>Pastor nuevo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analisis['actualizaciones'] as $a)
                                @continue(!$a['pastor_cambia'])
                                <tr>
                                    <td>{{ $a['codigo'] }}</td>
                                    <td>{{ $a['nombre'] }}</td>
                                    <td class="text-muted">{{ $a['pastor_anterior_texto'] ?: '—' }}</td>
                                    <td class="fw-semibold">{{ $a['pastor_nuevo_texto'] ?: '(queda vacío)' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Congregaciones nuevas (primeras 30)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Distrito</th>
                            <th>Pastor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (array_slice($analisis['nuevos'], 0, 30) as $n)
                            <tr>
                                <td>{{ $n['codigo'] }}</td>
                                <td>{{ $n['nombre'] }}</td>
                                <td>{{ $n['datos']['distrito'] }}</td>
                                <td>{{ $n['pastor_nuevo_texto'] ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No hay congregaciones nuevas en este archivo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-flex gap-2 mb-4">
        <form action="{{ route('maestras.congregacion.importar.confirmar') }}" method="POST"
            onsubmit="return confirm('Esto va a insertar {{ count($analisis['nuevos']) }} congregaciones nuevas y actualizar hasta {{ count($analisis['actualizaciones']) }} existentes. ¿Confirmas?');">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="archivo_nombre" value="{{ $archivoNombre }}">
            <button type="submit" class="btn btn-success">
                <i class="feather-check me-2"></i> Confirmar e importar
            </button>
        </form>
        <a href="{{ route('maestras.congregacion.importar.index') }}" class="btn btn-light">Cancelar</a>
    </div>
</x-base-layout>
