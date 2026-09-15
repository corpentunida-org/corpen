<x-base-layout>
    @section('titlepage', 'Revisar Importación CoMae_ter')

    <div class="col-lg-12">
        <div class="alert alert-info">
            <strong>Archivo:</strong> {{ $archivoNombre }} — {{ $analisis['total_filas'] }} filas leídas.
            Todavía no se ha guardado nada. Revisa el reporte y confirma abajo.
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-4 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ count($analisis['nuevos']) }}</h5>
                <span class="text-muted">TERCEROS NUEVOS A CREAR</span>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ count($analisis['enriquecer']) }}</h5>
                <span class="text-muted">EXISTENTES A COMPLETAR (solo vacíos)</span>
            </div>
        </div>
        <div class="col-xxl-4 col-md-6">
            <div class="card card-body">
                <h5 class="fs-4">{{ $analisis['ya_existen_sin_cambios_count'] }}</h5>
                <span class="text-muted">YA EXISTEN SIN NADA QUE RELLENAR</span>
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
                                <th># Campos</th>
                                <th>Campos que se van a rellenar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_slice($analisis['enriquecer'], 0, 30) as $e)
                                <tr>
                                    <td>{{ $e['cod_ter'] }}</td>
                                    <td>{{ $e['nombre'] }}</td>
                                    <td>{{ count($e['campos']) }}</td>
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
                <h5 class="card-title">Terceros nuevos (primeros 30)</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (array_slice($analisis['nuevos'], 0, 30) as $n)
                            <tr>
                                <td>{{ $n['cod_ter'] }}</td>
                                <td>{{ $n['nombre'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No hay terceros nuevos en este archivo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12 d-flex gap-2 mb-4">
        <form action="{{ route('maestras.comaeter.importar.confirmar') }}" method="POST"
            onsubmit="return confirm('Esto va a crear {{ count($analisis['nuevos']) }} terceros nuevos y completar campos vacíos en {{ count($analisis['enriquecer']) }} existentes. ¿Confirmas?');">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="archivo_nombre" value="{{ $archivoNombre }}">
            <button type="submit" class="btn btn-success">
                <i class="feather-check me-2"></i> Confirmar e importar
            </button>
        </form>
        <a href="{{ route('maestras.comaeter.importar.index') }}" class="btn btn-light">Cancelar</a>
    </div>
</x-base-layout>
