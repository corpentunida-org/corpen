<x-base-layout>
    @section('titlepage', 'Titulares')
    <x-success />
    <x-warning />

    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-sm-12 col-md-4">
                        @candirect('exequial.asociados.store')
                        <a href="{{ route('exequial.asociados.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Crear Titular</span>
                        </a>
                        @endcandirect
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <form action="{{ route('exequial.asociados.index') }}" method="GET"
                            class="d-flex align-items-center gap-2 justify-content-md-end">
                            <input type="text" name="buscar" value="{{ $busqueda }}" class="form-control form-control-sm"
                                style="max-width: 260px;" placeholder="Cédula o nombre" autofocus>
                            <select name="estado" class="form-select form-select-sm" style="max-width: 140px;">
                                <option value="" @selected($estadoFiltro === null || $estadoFiltro === '')>Todos</option>
                                <option value="activo" @selected($estadoFiltro === 'activo')>Activos</option>
                                <option value="inactivo" @selected($estadoFiltro === 'inactivo')>Inactivos</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
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
                @if (!$sePresentoFiltro)
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-search fs-1 d-block mb-2"></i>
                        Usa el buscador o el filtro de estado de arriba para ver titulares.
                    </div>
                @else
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
                                        <td class="text-end">
                                            <div class="d-flex gap-2 justify-content-end align-items-center">
                                                @candirect('exequial.retiros.store')
                                                    @if ($t->estado)
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal" data-bs-target="#modalRetiro"
                                                            data-cedula="{{ $t->cod_cli }}"
                                                            data-nombre="{{ $t->nombre ?: $t->cod_cli }}">
                                                            <i class="bi bi-person-dash me-1"></i> Retirar
                                                        </button>
                                                    @endif
                                                @endcandirect
                                                @candirect('exequial.retiros.reafiliar')
                                                    @if (!$t->estado && $retirados->has($t->cod_cli))
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal" data-bs-target="#modalReafiliar"
                                                            data-cedula="{{ $t->cod_cli }}"
                                                            data-nombre="{{ $t->nombre ?: $t->cod_cli }}">
                                                            <i class="bi bi-person-check me-1"></i> Reintegro
                                                        </button>
                                                    @endif
                                                @endcandirect
                                                <div class="dropdown">
                                                    <button class="btn btn-success" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical me-1"></i> Opciones
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}?id={{ $t->cod_cli }}">
                                                                <i class="feather feather-eye me-2"></i> Ver titular
                                                            </a>
                                                        </li>
                                                        @candirect('exequial.beneficiarios.index')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}?id={{ $t->cod_cli }}#beneficiarios">
                                                                    <i class="bi bi-people me-2"></i> Beneficiarios
                                                                </a>
                                                            </li>
                                                        @endcandirect
                                                        @candirect('exequial.asociados.update')
                                                            @if ($t->estado)
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('exequial.asociados.edit', $t->cod_cli) }}">
                                                                        <i class="feather feather-edit-2 me-2"></i> Editar titular
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endcandirect
                                                    </ul>
                                                </div>
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
                                    <input type="date" name="fecha_retiro" class="form-control"
                                        min="{{ now()->subMonths(6)->toDateString() }}" max="{{ date('Y-m-d') }}" required>
                                    <small class="text-muted">Máximo 6 meses atrás, y posterior a la fecha de afiliación.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="marcar_pastor_retirado"
                                            id="marcarPastorRetirado" value="1">
                                        <label class="form-check-label" for="marcarPastorRetirado">
                                            Marcar también como "Retirado" en el módulo de Pastores/Asociados
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Retirar del plan de Exequiales no cambia por sí solo el estado del pastor.
                                        Solo marca esta casilla si este retiro también es un retiro pastoral.
                                    </small>
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

    @candirect('exequial.retiros.reafiliar')
        <div class="modal fade" id="modalReafiliar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" id="formReafiliar" action="">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Reintegro de titular</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">
                                Vas a reintegrar a <strong id="reafiliarNombre"></strong> (cédula <span id="reafiliarCedula"></span>).
                                Esto también notifica a la API externa y reactiva al titular.
                            </p>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase">Observación del reintegro</label>
                                <textarea name="observacion_reafiliacion" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Confirmar reintegro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.getElementById('modalReafiliar').addEventListener('show.bs.modal', function (event) {
                    var boton = event.relatedTarget;
                    var cedula = boton.getAttribute('data-cedula');
                    var nombre = boton.getAttribute('data-nombre');
                    document.getElementById('reafiliarNombre').textContent = nombre;
                    document.getElementById('reafiliarCedula').textContent = cedula;
                    document.getElementById('formReafiliar').action = '{{ url('exequiales/asociados') }}/' + cedula + '/reafiliar';
                });
            </script>
        @endpush
    @endcandirect
</x-base-layout>
