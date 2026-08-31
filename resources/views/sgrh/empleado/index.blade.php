<x-base-layout>

    {{-- ENCABEZADO --}}
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Colaboradores</h2>
            <p class="text-muted mb-0">Listado de colaboradores identificados a partir del maestro de terceros.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('sgrh.empleado.create') }}" class="btn btn-primary px-4">
                <i class="bi bi-plus-circle"></i> Registrar colaborador
            </a>
        </div>
    </div>

    {{-- BÚSQUEDA Y FILTROS --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sgrh.empleado.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" name="search" class="form-control border-start-0"
                               placeholder="Buscar por nombre o código de tercero..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                        <option value="retirado" @selected(request('estado') === 'retirado')>Retirado</option>
                    </select>
                </div>
                <div class="col-md-3 text-md-end">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                    <span class="text-muted small ms-2">Total: <strong>{{ $empleados->total() }}</strong></span>
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
                        <th>Código (cod_ter)</th>
                        <th>Fecha de ingreso</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Cambiar estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($empleados as $empleado)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">{{ $empleado->nombre_completo ?: 'Tercero no encontrado' }}</div>
                                @if ($empleado->tercero)
                                    <div class="text-muted small">
                                        {{ $empleado->tercero->email }}
                                        @can('sgrh.tercero.edit')
                                            <a href="{{ route('sgrh.tercero.edit', $empleado->cod_ter) }}"
                                               class="ms-2" data-bs-toggle="tooltip" title="Consultar / corregir datos del tercero">
                                                <i class="bi bi-pencil-square"></i> Editar tercero
                                            </a>
                                        @elsecan('sgrh.tercero.show')
                                            <a href="{{ route('sgrh.tercero.show', $empleado->cod_ter) }}"
                                               class="ms-2" data-bs-toggle="tooltip" title="Consultar datos del tercero (solo lectura)">
                                                <i class="bi bi-eye"></i> Ver tercero
                                            </a>
                                        @endcan
                                        @if ($tipoEmpleadoId === null || (int) $empleado->tercero->tip_prv !== $tipoEmpleadoId)
                                            <span class="badge bg-warning-subtle text-warning ms-1" data-bs-toggle="tooltip"
                                                  title="Este tercero no está clasificado como &quot;Empleado&quot; en el maestro de terceros (Tipo de Tercero)">
                                                <i class="bi bi-exclamation-triangle"></i> Sin clasificar
                                            </span>
                                        @endif
                                        @if (!$empleado->tercero->fec_act || (string) $empleado->tercero->fec_act < $fechaLimiteActualizacion)
                                            <span class="badge rounded-pill ms-1 px-2 py-1" style="background-color: #ffe4e6; color: #e11d48; font-weight: 600;" data-bs-toggle="tooltip"
                                                  title="Última actualización: {{ $empleado->tercero->fec_act ? \Illuminate\Support\Carbon::parse($empleado->tercero->fec_act)->format('d/m/Y') : 'nunca registrada' }}">
                                                <i class="bi bi-exclamation-triangle"></i> Información de usuario requiere actualizar
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-danger small">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        No se encontró el tercero con cod_ter {{ $empleado->cod_ter }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $empleado->cod_ter }}</td>
                            <td>{{ $empleado->fecha_ingreso?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-center">
                                @switch($empleado->estado)
                                    @case('activo')
                                        <span class="badge bg-success-subtle text-success">Activo</span>
                                        @break
                                    @case('retirado')
                                        <span class="badge bg-danger-subtle text-danger">Retirado</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning-subtle text-warning">Inactivo</span>
                                @endswitch
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('sgrh.empleado.updateEstado', $empleado->id) }}" method="POST"
                                      class="d-inline-flex gap-2 justify-content-end">
                                    @csrf
                                    @method('PUT')
                                    <select name="estado" class="form-select form-select-sm" style="width: auto;">
                                        <option value="activo" @selected($empleado->estado === 'activo')>Activo</option>
                                        <option value="inactivo" @selected($empleado->estado === 'inactivo')>Inactivo</option>
                                        <option value="retirado" @selected($empleado->estado === 'retirado')>Retirado</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Guardar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                No hay colaboradores registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($empleados->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small text-muted mb-0">
                        Mostrando registros del {{ $empleados->firstItem() }} al {{ $empleados->lastItem() }}
                    </p>
                    {{ $empleados->links() }}
                </div>
            </div>
        @endif
    </div>

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
