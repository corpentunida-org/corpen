<x-base-layout>
    @section('titlepage', 'Revisar Importación de Pastores IPUC')

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
                <span class="text-muted">PASTORES NUEVOS A CREAR</span>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ count($analisis['enriquecer']) }}</h5>
                <span class="text-muted">EXISTENTES A COMPLETAR (solo vacíos)</span>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ $analisis['ya_existen_sin_cambios_count'] }}</h5>
                <span class="text-muted">YA EXISTEN SIN NADA QUE RELLENAR</span>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card card-body {{ count($analisis['excepciones']) > 0 ? 'border border-danger' : '' }}">
                <h5 class="fs-4">{{ count($analisis['excepciones']) }}</h5>
                <span class="text-muted">EXCEPCIONES (no se van a crear)</span>
            </div>
        </div>
    </div>

    @if (count($analisis['duplicados']) > 0)
        <div class="col-lg-12">
            <div class="alert alert-warning">
                <strong>{{ count($analisis['duplicados']) }} cédula(s) duplicada(s) dentro del mismo Excel</strong> —
                solo se toma la primera aparición de cada una: {{ implode(', ', array_slice($analisis['duplicados'], 0, 20)) }}
            </div>
        </div>
    @endif

    @if (count($analisis['excepciones']) > 0)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title text-danger">Excepciones — estos pastores NO se van a crear</h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analisis['excepciones'] as $exc)
                                <tr>
                                    <td>{{ $exc['cod_ter'] }}</td>
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

    @if (count($analisis['enriquecer']) > 0)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Existentes a completar (primeros 30) — solo campos que hoy tienen vacíos</h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Campos que se van a rellenar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_slice($analisis['enriquecer'], 0, 30) as $e)
                                <tr>
                                    <td>{{ $e['cod_ter'] }}</td>
                                    <td>{{ $e['nombre'] }}</td>
                                    <td class="small">{{ implode(', ', array_keys($e['campos'])) }}</td>
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
                <h5 class="card-title">Pastores nuevos (primeros 30)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Congregación</th>
                            <th>Distrito</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (array_slice($analisis['nuevos'], 0, 30) as $n)
                            <tr>
                                <td>{{ $n['cod_ter'] }}</td>
                                <td>{{ $n['nombre'] }}</td>
                                <td>{{ $n['datos']['congrega'] ?? '—' }}</td>
                                <td>{{ $n['datos']['cod_dist'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No hay pastores nuevos en este archivo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-flex gap-2 mb-4">
        <form action="{{ route('maestras.terceros.importar.confirmar') }}" method="POST"
            onsubmit="return confirm('Esto va a crear {{ count($analisis['nuevos']) }} pastores nuevos y completar campos vacíos en {{ count($analisis['enriquecer']) }} existentes. ¿Confirmas?');">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="archivo_nombre" value="{{ $archivoNombre }}">
            <button type="submit" class="btn btn-success">
                <i class="feather-check me-2"></i> Confirmar e importar
            </button>
        </form>
        <a href="{{ route('maestras.terceros.importar.index') }}" class="btn btn-light">Cancelar</a>
    </div>
</x-base-layout>
