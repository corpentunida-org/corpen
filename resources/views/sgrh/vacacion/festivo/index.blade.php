<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Festivos de Colombia</h2>
            <p class="text-muted mb-0">Calculados automáticamente cada año. Puedes agregar una fecha adicional (festivo local/empresarial) o excluir una que no deba aplicar.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <form method="GET" action="{{ route('sgrh.vacacion.festivo.index') }}" class="d-flex justify-content-end gap-2">
                <select name="anio" class="form-select" style="max-width: 140px;" onchange="this.form.submit()">
                    @foreach (range(now()->year - 1, now()->year + 2) as $anioOpcion)
                        <option value="{{ $anioOpcion }}" @selected($anio === $anioOpcion)>{{ $anioOpcion }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Festivos de {{ $anio }} (con ajustes ya aplicados)</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Día</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($festivos as $festivo)
                                    <tr>
                                        <td class="fw-bold">{{ $festivo->format('d/m/Y') }}</td>
                                        <td class="text-muted small text-capitalize">{{ $festivo->translatedFormat('l') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-3 text-center text-muted">Sin festivos calculados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Ajustes manuales</h5>
                        @can('sgrh.vacacion.festivo.store')
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAjusteFestivo">
                                <i class="bi bi-plus-circle"></i> Nuevo
                            </button>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ajustes as $ajuste)
                                    <tr>
                                        <td>
                                            {{ $ajuste->fecha->format('d/m/Y') }}
                                            @if ($ajuste->descripcion)
                                                <div class="small text-muted">{{ $ajuste->descripcion }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ajuste->tipo === 'agregado')
                                                <span class="badge bg-success-subtle text-success">Agregado</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Excluido</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @can('sgrh.vacacion.festivo.destroy')
                                                <form action="{{ route('sgrh.vacacion.festivo.destroy', $ajuste) }}" method="POST" onsubmit="return confirm('¿Eliminar este ajuste?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-3 text-center text-muted">Sin ajustes registrados para este año.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('sgrh.vacacion.festivo.store')
        <div class="modal fade" id="modalAjusteFestivo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('sgrh.vacacion.festivo.store') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Nuevo ajuste de festivo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Tipo</label>
                                    <select name="tipo" class="form-select">
                                        <option value="agregado">Agregar como festivo</option>
                                        <option value="excluido">Excluir (no es festivo)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Descripción (opcional)</label>
                                    <input type="text" name="descripcion" class="form-control" maxlength="255" placeholder="Ej. Día de la empresa">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @push('scripts')
        <script>
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif
        </script>
    @endpush
</x-base-layout>
