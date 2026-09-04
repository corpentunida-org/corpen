<x-base-layout>

    {{-- ENCABEZADO --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Contratos</h2>
            <p class="text-muted mb-0">Historial contractual de todos los colaboradores.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sgrh.contrato.alertas') }}" class="btn btn-outline-danger me-2">
                <i class="bi bi-exclamation-triangle"></i> Próximos a vencer
            </a>
            @can('sgrh.contrato.store')
                <a href="{{ route('sgrh.contrato.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle"></i> Registrar contrato
                </a>
            @endcan
        </div>
    </div>

    {{-- BÚSQUEDA Y FILTROS --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sgrh.contrato.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" class="form-control border-start-0"
                               placeholder="Buscar por nombre del colaborador..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        @foreach (['Activo', 'Vencido', 'Liquidado', 'Renovado'] as $opcion)
                            <option value="{{ $opcion }}" @selected(request('estado') === $opcion)>{{ $opcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipo_contrato_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        @foreach ($tiposContrato as $tipo)
                            <option value="{{ $tipo->id }}" @selected(request('tipo_contrato_id') == $tipo->id)>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-md-end">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                    <span class="text-muted small ms-2">Total: <strong>{{ $contratos->total() }}</strong></span>
                </div>
            </form>
        </div>
    </div>

    {{-- LISTADO --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Colaborador</th>
                        <th>Tipo</th>
                        <th>Cargo</th>
                        <th>Inicio</th>
                        <th>Vencimiento</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contratos as $contrato)
                        <tr>
                            <td class="ps-4 py-3 fw-bold">{{ $contrato->empleado->nombre_completo ?: 'Tercero no encontrado' }}</td>
                            <td class="text-muted small">{{ $contrato->tipoContrato->nombre }}</td>
                            <td class="text-muted small">{{ $contrato->cargo->nombre ?? '—' }}</td>
                            <td>{{ $contrato->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}</td>
                            <td>
                                {{ $contrato->fecha_vencimiento?->format('d/m/Y') ?? 'Indefinido' }}
                                @if ($contrato->estado === 'Activo' && $contrato->estaVencido)
                                    <span class="badge rounded-pill ms-1 px-2 py-1" style="background-color: #ffe4e6; color: #e11d48; font-weight: 600;">
                                        <i class="bi bi-exclamation-triangle"></i> Vencido
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($contrato->estado)
                                    @case('Activo')
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                        @break
                                    @case('Vencido')
                                        <span class="badge bg-danger-subtle text-danger">Vencido</span>
                                        @break
                                    @case('Liquidado')
                                        <span class="badge bg-secondary-subtle text-secondary">Liquidado</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning-subtle text-warning">Renovado</span>
                                @endswitch
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown" data-bs-strategy="fixed">
                                        <i class="feather feather-more-horizontal"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @can('sgrh.contrato.update')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('sgrh.contrato.edit', $contrato) }}">
                                                    <i class="feather-edit-3 me-2"></i> Editar
                                                </a>
                                            </li>
                                            @if ($contrato->estado === 'Activo')
                                                <li>
                                                    <form action="{{ route('sgrh.contrato.renovar', $contrato) }}" method="POST"
                                                          onsubmit="return confirm('¿Cerrar este contrato y registrar uno nuevo?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="feather-refresh-cw me-2"></i> Renovar
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        @endcan
                                        @if ($contrato->documento_url)
                                            <li>
                                                <a class="dropdown-item" href="{{ $contrato->documento_url }}" target="_blank" rel="noopener">
                                                    <i class="bi bi-file-earmark-pdf me-2"></i> Ver documento
                                                </a>
                                            </li>
                                        @endif
                                        @can('sgrh.contrato.destroy')
                                            <li>
                                                <form action="{{ route('sgrh.contrato.destroy', $contrato) }}" method="POST"
                                                      onsubmit="return confirm('¿Eliminar este contrato de forma permanente, junto con su historial de modificaciones? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash3 me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">No hay contratos registrados todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contratos->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">
                        Mostrando registros del {{ $contratos->firstItem() }} al {{ $contratos->lastItem() }}
                    </p>
                    {{ $contratos->links() }}
                </div>
            </div>
        @endif
    </div>

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
            @endif
        </script>
    @endpush
</x-base-layout>
