<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Políticas de vacaciones</h2>
            <p class="text-muted mb-0">Días por año, adelantos y topes de acumulación. Cada cambio queda versionado, nunca se edita una política ya vigente.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @can('sgrh.vacacion.politica.store')
                <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#modalPolitica">
                    <i class="bi bi-plus-circle"></i> Nueva versión de política
                </button>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Vigente desde</th>
                        <th>Días/año</th>
                        <th>Tipo de días</th>
                        <th>Máx. acumulable</th>
                        <th>Adelanto</th>
                        <th>Configurada por</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($politicas as $politica)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $politica->vigente_desde->format('d/m/Y') }}</td>
                            <td>{{ $politica->dias_por_anio }}</td>
                            <td class="text-muted small">{{ $politica->tipo_dias === 'habiles' ? 'Hábiles' : 'Calendario' }}</td>
                            <td class="text-muted small">{{ $politica->max_dias_acumulables ?? 'Sin tope' }}</td>
                            <td class="text-muted small">
                                @if ($politica->permite_adelanto)
                                    Permitido{{ $politica->max_dias_adelanto ? " (máx. {$politica->max_dias_adelanto} días)" : '' }}
                                @else
                                    No permitido
                                @endif
                            </td>
                            <td class="text-muted small">{{ $politica->usuario->name ?? '—' }}</td>
                            <td class="text-center">
                                @if ($politica->activa)
                                    <span class="badge bg-success-subtle text-success">Vigente</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Histórica</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">No hay ninguna política configurada todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($politicas->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $politicas->links() }}
            </div>
        @endif
    </div>

    @can('sgrh.vacacion.politica.store')
        <div class="modal fade" id="modalPolitica" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('sgrh.vacacion.politica.store') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Nueva versión de política</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Días por año</label>
                                    <input type="number" name="dias_por_anio" class="form-control" min="1" max="60" value="15" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Tipo de días</label>
                                    <select name="tipo_dias" class="form-select">
                                        <option value="habiles" selected>Hábiles</option>
                                        <option value="calendario">Calendario</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Máximo acumulable</label>
                                    <input type="number" name="max_dias_acumulables" class="form-control" min="0" max="255" placeholder="Sin tope">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Vigente desde</label>
                                    <input type="date" name="vigente_desde" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="permite_adelanto" id="permite_adelanto" class="form-check-input" value="1" checked
                                               onchange="document.getElementById('wrapper_max_adelanto').classList.toggle('d-none', !this.checked)">
                                        <label class="form-check-label" for="permite_adelanto">Permite vacaciones adelantadas</label>
                                    </div>
                                </div>
                                <div class="col-12" id="wrapper_max_adelanto">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Máximo de días de adelanto (saldo negativo permitido)</label>
                                    <input type="number" name="max_dias_adelanto" class="form-control" min="0" max="255" placeholder="Sin tope explícito">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
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
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
                new bootstrap.Modal(document.getElementById('modalPolitica')).show();
            @endif
        </script>
    @endpush
</x-base-layout>
