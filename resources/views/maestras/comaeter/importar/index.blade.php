<x-base-layout>
    @section('titlepage', 'Actualizar Terceros (CoMae_ter)')
    <x-success />

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Subir export CoMae_ter (Excel)</h5>
                <a href="{{ route('maestras.comaeter.importar.plantilla') }}" data-no-loading class="btn btn-sm btn-outline-primary">
                    <i class="feather-download me-1"></i> Descargar plantilla
                </a>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Este archivo es el volcado general de Terceros del sistema externo (no solo pastores).
                    Los terceros que <strong>no existen todavía</strong> en Maestra de Terceros (por cédula) se crean.
                    Los que <strong>ya existen</strong> nunca se sobrescriben — solo se rellenan los campos que hoy
                    tienen vacíos, en cualquiera de las columnas que coincidan con el Excel. Congregación y distrito
                    no se tocan desde aquí, solo desde Congregaciones. En el siguiente paso vas a ver el reporte
                    completo antes de que se guarde nada.
                </p>
                <form action="{{ route('maestras.comaeter.importar.analizar') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label">Archivo Excel (.xlsx/.xls)</label>
                        <input type="file" name="archivo" class="form-control @error('archivo') is-invalid @enderror" accept=".xlsx,.xls" required>
                        @error('archivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="feather-upload me-2"></i> Analizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Historial de importaciones</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Archivo</th>
                                <th>Nuevos</th>
                                <th>Enriquecidos</th>
                                <th>Ya existían</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($importaciones as $imp)
                                <tr>
                                    <td>{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $imp->user->name ?? '—' }}</td>
                                    <td>{{ $imp->archivo_nombre }}</td>
                                    <td>{{ $imp->resumen['insertados'] ?? 0 }}</td>
                                    <td>{{ $imp->resumen['enriquecidos'] ?? 0 }}</td>
                                    <td>{{ $imp->resumen['ya_existian'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Todavía no se ha corrido ninguna importación.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($importaciones->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4">
                        {{ $importaciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-base-layout>
