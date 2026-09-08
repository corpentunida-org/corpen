<x-base-layout>
    @section('titlepage', 'Titulares')
    <x-success />
    <x-warning />

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-12 col-md-4">
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                                    @candirect('exequial.asociados.store')
                                    <a href="{{ route('exequial.asociados.create') }}" class="btn btn-primary">
                                        <i class="feather-plus me-2"></i>
                                        <span>Crear Titular</span>
                                    </a>
                                    @endcandirect
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <form action="{{ route('exequial.asociados.index') }}" method="GET"
                            class="d-flex align-items-center gap-2">
                            <input type="text" name="buscar" value="{{ $busqueda }}" class="form-control form-control-sm"
                                placeholder="Cédula o nombre">
                            <select name="estado" class="form-select form-select-sm" style="max-width: 140px;">
                                <option value="" @selected($estadoFiltro === null || $estadoFiltro === '')>Todos</option>
                                <option value="activo" @selected($estadoFiltro === 'activo')>Activos</option>
                                <option value="inactivo" @selected($estadoFiltro === 'inactivo')>Inactivos</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-4 d-flex justify-content-end align-items-center">
                        <form action="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}" method="GET"
                            class="d-flex align-items-center gap-2">
                            <label for="valueCedula" class="mb-0 me-2 text-nowrap">Buscar exacto:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="cédula titular">
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Listado de titulares</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Fecha afiliación</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($titulares as $t)
                                <tr>
                                    <td class="fw-semibold">{{ $t->cod_cli }}</td>
                                    <td>{{ $t->nombre ?: 'Sin nombre registrado' }}</td>
                                    <td class="text-muted small">{{ $t->fec_ing ? \Carbon\Carbon::parse($t->fec_ing)->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @if ($t->estado)
                                            <span class="badge bg-soft-success text-success">Activo</span>
                                        @elseif ($retirados->has($t->cod_cli))
                                            <span class="badge bg-soft-warning text-warning">Retirado</span>
                                        @else
                                            <span class="badge bg-gray-200 text-dark">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}?id={{ $t->cod_cli }}"
                                                class="avatar-text avatar-md" data-bs-toggle="tooltip" title="Ver"
                                                data-bs-original-title="Ver">
                                                <i class="feather feather-eye"></i>
                                            </a>
                                            @candirect('exequial.retiros.store')
                                                @if ($t->estado)
                                                    <a href="javascript:void(0);" class="avatar-text avatar-md text-danger"
                                                        data-bs-toggle="modal" data-bs-target="#modalRetiro"
                                                        data-cedula="{{ $t->cod_cli }}"
                                                        data-nombre="{{ $t->nombre ?: $t->cod_cli }}"
                                                        title="Retirar titular">
                                                        <i class="bi bi-person-dash"></i>
                                                    </a>
                                                @endif
                                            @endcandirect
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay titulares para los filtros seleccionados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($titulares->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4">
                        {{ $titulares->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @candirect('exequial.retiros.store')
        <div class="modal fade" id="modalRetiro" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" id="formRetiro" action="">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Retirar titular</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">
                                Vas a retirar a <strong id="retiroNombre"></strong> (cédula <span id="retiroCedula"></span>).
                                Esto también notifica a la API externa y desactiva al titular.
                            </p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Fecha de retiro</label>
                                    <input type="date" name="fecha_retiro" class="form-control" max="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Confirmar retiro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.getElementById('modalRetiro').addEventListener('show.bs.modal', function (event) {
                    var boton = event.relatedTarget;
                    var cedula = boton.getAttribute('data-cedula');
                    var nombre = boton.getAttribute('data-nombre');
                    document.getElementById('retiroNombre').textContent = nombre;
                    document.getElementById('retiroCedula').textContent = cedula;
                    document.getElementById('formRetiro').action = '{{ url('exequiales/asociados') }}/' + cedula + '/retiro';
                });
            </script>
        @endpush
    @endcandirect
</x-base-layout>
