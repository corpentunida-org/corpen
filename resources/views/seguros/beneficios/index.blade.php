<x-base-layout>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body lead-status">
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
                        <div class="col-xxl-5 col-md-6  mb-4">
                            <label class="form-label">Edad</label>
                            <div class="row">
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
                        <div class="col-xxl-3 col-md-2  mb-4">
                            <label class="form-label">Tipo Afiliado</label>
                            <select class="form-control" name="tipo">
                                <option value="AF">Titular</option>
                                <option value="CO">Conyugue</option>
                                <option value="TODOS">Todos</option>
                            </select>
                        </div>
                        <div class="col-xxl-4 col-md-3  mb-4">
                            <label class="form-label">Plan</label>
                            <select class="form-control" name="plan">
                                <option value="todos">Todos</option>
                                @foreach ($planes as $plan)
                                    <option value="{{ $plan->valor }}">{{ $plan->name }} -
                                        ${{ number_format($plan->valor) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-md btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if (isset($listadata))
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Lista de datos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 mb-4">
                            <label class="form-label">Descuento Porcentaje</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="despor">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-lg-2 mb-4">
                            <label class="form-label">Descuento Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="desval" value="0">
                            </div>
                        </div>
                        <div class="col-lg-8 mb-4">

                            <label class="form-label">Observación<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="observaciones" required>

                        </div>
                    </div>

                    <div class="mb-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-md btn-primary">Aplicar</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
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
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="javascript:void(0);">
                                                    <span class="d-block">{{ $i->seg_asegurado_id ?? '' }}</span>
                                                    <span
                                                        class="fs-12 d-block fw-normal text-muted text-wrap">{{ $i->tercero->nombre ?? '' }}</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td><span
                                                class="badge bg-gray-200 text-dark">{{ $i->asegurado->parentesco }}</span>
                                        </td>
                                        <td><a>{{$i->tercero->edad}}</a></td>
                                        <td>
                                            <a><span class="fs-12 fw-normal text-muted"> {{$i->plan->name}} - </span> 
                                                 ${{ number_format($i->plan->valor) }}</a>
                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">Valor Prima: ${{number_format($i->plan->prima)}}</p>
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        $(document).ready(function() {
            $('#formFiltroBeneficios').submit(function(event) {
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
