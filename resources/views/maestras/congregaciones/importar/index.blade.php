<x-base-layout>
    @section('titlepage', 'Actualizar Congregaciones')
    <x-success />

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Subir listado de congregaciones (Excel)</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Sube el archivo mensual de congregaciones. En el siguiente paso vas a ver un reporte completo
                    de qué se va a crear, qué va a cambiar (pastor, distrito) y qué filas quedan fuera por algún
                    problema — nada se guarda todavía en este paso.
                </p>
                <form action="{{ route('maestras.congregacion.importar.analizar') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label">Archivo Excel (.xlsx)</label>
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
                                <th>Nuevas</th>
                                <th>Actualizadas</th>
                                <th>Pastores enlazados</th>
                                <th>Excepciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($importaciones as $imp)
                                <tr>
                                    <td>{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $imp->user->name ?? '—' }}</td>
                                    <td>{{ $imp->archivo_nombre }}</td>
                                    <td>{{ $imp->resumen['insertados'] ?? 0 }}</td>
                                    <td>{{ $imp->resumen['actualizados'] ?? 0 }}</td>
                                    <td>{{ $imp->resumen['pastores_enlazados'] ?? 0 }}</td>
                                    <td>{{ $imp->resumen['excepciones'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Todavía no se ha corrido ninguna importación.</td>
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
