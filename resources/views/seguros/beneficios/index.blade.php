<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Crear beneficios: </span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">Lista de filtros </span>
                    </h5>
                </div>
                <form class="row" method="post" action="{{ route('seguros.beneficios.list') }}"
                    id="formFiltroBeneficios" novalidate>
                    @method('POST')
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-4">
                            <label class="form-label">Edad</label>
                            <div class="row pt-3">
                                <div class="col-xxl-6 col-md-6">
                                    <input type="number" class="form-control" name="edad_minima"
                                        placeholder="Edad mínima" required>
                                </div>
                                <div class="col-xxl-6 col-md-6">
                                    <input type="number" class="form-control" name="edad_maxima"
                                        placeholder="Edad máxima" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mb-4">
                            <label class="form-label">Tipo Afiliado</label>
                            <select class="form-control mt-3" name="tipo">
                                <option value="AF">TITULAR</option>
                                <option value="CO">CONYUGUE</option>
                                <option value="HI">HIJO</option>
                                <option value="HE">HERMANO</option>
                                <option value="VIUDA">VIUDA</option>
                                <option value="TODOS">TODOS</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-5 mb-4">
                            <label class="form-label">Planes</label>
                            <div class="row">
                                @php
                                    $columnas = array_chunk($planes->toArray(), ceil(count($planes) / 2));
                                @endphp
                                @foreach ($columnas as $index => $columna)
                                    <div class="col-6">
                                        @if ($index === 0)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="planesTODOS" value="true"
                                                    id="planesTODOS" required>
                                                <label class="form-check-label" for="planesTODOS">
                                                    TODOS
                                                </label>
                                            </div>
                                        @endif

                                        @foreach ($columna as $plan)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="planes[]"
                                                    value="{{ $plan['valor'] }}">
                                                <label class="form-check-label">
                                                    {{ $plan['name'] }} - ${{ number_format($plan['valor']) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-end gap-2 ">
                                <button type="submit" class="btn btn-md btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if (isset($listadata))
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Lista de datos</h5>
                    </div>
                    <div class="card-body">
                        @if ($listadata->isEmpty())
                            <div class="alert alert-danger" role="alert">
                                No se encontraron resultados para los filtros seleccionados.
                            </div>
                        @else
                                <form action="{{ route('seguros.beneficios.store') }}" method="post" id="formAddBeneficios"
                                    class="row" novalidate>
                                    @method('POST')
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-2 mb-4">
                                            <label class="form-label">Descuento Porcentaje</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" value="0" name="despor" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 mb-4">
                                            <label class="form-label">Descuento Valor</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="desval" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 mb-4">
                                            <label class="form-label">Observación<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control text-uppercase" name="observaciones" required>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-md btn-primary">Aplicar</button>
                                    </div>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line text-end pt-1">
                                        Se encontraron <span style="font-weight: bold">{{$listadata->count()}}</span> pólizas</span>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Cédula</th>
                                                    <th>Asegurado</th>
                                                    <th>Parentesco</th>
                                                    <th>Edad</th>
                                                    <th>Plan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($listadata as $i)
                                                    <tr>
                                                        <td>
                                                            <a>
                                                                <span class="d-block">{{ $i->seg_asegurado_id ?? '' }}</span>
                                                                <input type="hidden" name="beneficio[{{ $loop->index }}][cedula]"
                                                                    value="{{ $i->seg_asegurado_id }}">
                                                            </a>

                                                        </td>
                                                        <td><span
                                                                class="fs-12 d-block fw-normal text-wrap">{{ $i->tercero->nombre ?? '' }}</span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-gray-200 text-dark">{{ $i->asegurado->parentesco }}</span>
                                                        </td>
                                                        <td><a>{{$i->tercero->edad}}</a></td>
                                                        <td>
                                                            <a><span class="fs-12 fw-normal text-muted"> {{$i->plan->name}} - </span>
                                                                ${{ number_format($i->plan->valor) }}</a>
                                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">Valor
                                                                Prima:
                                                                ${{number_format($i->plan->prima)}}</p>
                                                        </td>
                                                        <input type="hidden" name="beneficio[{{ $loop->index }}][poliza]"
                                                            value="{{ $i->id }}">
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <input type="hidden" name="grupo" value=true>
                                </form>
                            </div>
                        @endif
                </div>
            </div>
        @endif
        <script>
            $(document).ready(function () {
                $('#formFiltroBeneficios').submit(function (event) {
                    var form = this;
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        $(form).addClass('was-validated');
                    }
                });
                $('#formAddBeneficios').submit(function (event) {
                    var form = this;
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        $(form).addClass('was-validated');
                    }
                });
            });
        </script>
</x-base-layout>