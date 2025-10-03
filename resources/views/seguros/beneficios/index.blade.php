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
                        @if ($listadata->isnotEmpty())
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <form action="{{ route('seguros.poliza.filtros') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="listadata" value="{{ json_encode($listadata) }}">
                                    <button type="submit" class="btn btn-sm bg-soft-teal text-teal">Descargar PDF</button>
                                </form>
                                <form action="{{route('seguros.poliza.filtroexcel')}}" method="POST">
                                    @csrf
                                    <input type="hidden" name="listadata" value="{{ json_encode($listadata) }}">
                                    <button type="submit" class="btn btn-sm bg-soft-warning text-warning">Descargar
                                        Excel</button>
                                </form>
                            </div>
                        @endif
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
                                        <div class="col-lg-2">
                                            <label class="form-label">Descuento Valor</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="desval" id="inputdesval" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <label class="form-label">Descuento Porcentaje</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" value="0" name="despor" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <label class="form-label">Observación<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control text-uppercase" name="observaciones" required>
                                        </div>
                                        <div class="fs-12 fw-normal text-muted text-truncate-1-line pt-1">
                                            <div class="custom-control custom-checkbox" style="display: none;"
                                                id="checkvalbeneficio">
                                                <input type="checkbox" class="form-check-input ml-3" id="checkbox2"
                                                    name="checkconfirmarbene" value=true>
                                                <label class="form-check-label" for="checkbox2" id="labeltextbeneficio">Confirmar
                                                    restar el valor de descuento al valor prima</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="grupo" value=true>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="submit" class="btn btn-md btn-primary">Aplicar</button>
                                    </div>                                    
                                    <h3 class="mb-3">Se encontraron <span class="text-primary">{{$listadata->count()}}</span> pólizas</h3>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Cédula</th>
                                                    <th>Asegurado</th>
                                                    <th>Parentesco</th>
                                                    <th>Edad</th>
                                                    <th>Plan</th>
                                                    <th class="text-end">Acción</th>
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
                                                                class="fs-12 d-block fw-normal text-wrap">{{ $i->tercero->nom_ter ?? '' }}</span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-gray-200 text-dark">{{ $i->asegurado->parentesco }}</span>
                                                        </td>
                                                        <td><a>{{$i->tercero->edad}}</a></td>
                                                        <td>
                                                            <a><span class="fs-12 fw-normal text-muted"> {{$i->plan->name ?? ''}} -
                                                                </span>
                                                                ${{ number_format($i->valor_asegurado ?? '0') }}</a>
                                                            <p class="fs-12 text-muted text-truncate-1-line tickets-sort-desc">Valor
                                                                Prima:
                                                                ${{number_format($i->primapagar ?? '0')}}</p>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <a href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $i->seg_asegurado_id }}"
                                                                    class="avatar-text avatar-md" data-bs-toggle="tooltip" title=""
                                                                    data-bs-original-title="Ver detalle póliza">
                                                                    <i class="feather-arrow-right"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <input type="hidden" name="beneficio[{{ $loop->index }}][poliza]"
                                                            value="{{ $i->id }}">
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </form>
                            </div>
                        @endif
                </div>
            </div>
        @endif
        @if(isset($beneficios))
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Historial Beneficios Registrados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="customerList">
                                <thead>
                                    <tr>
                                        <th>Asegurado</th>
                                        <th>Valor Descuento</th>
                                        <th>Valor Actual a Pagar</th>
                                        <th>Observación</th>
                                        <th>Fecha Registro</th>
                                        <th class="text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($beneficios as $b)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <a href="javascript:void(0);">
                                                        <span class="d-block">{{ $b->tercero->nom_ter ?? ''}}</span>
                                                        <span
                                                            class="fs-12 d-block fw-normal text-muted text-wrap">{{ $b->cedulaAsegurado ?? ''}}</span>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>$ {{ number_format($b->valorDescuento) }} </td>
                                            <td>$ {{$b->polizarel->primapagar}}</td>
                                            <td><span
                                                    class="badge bg-soft-primary text-primary text-wrap">{{ $b->observaciones }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <a>
                                                        <span class="fs-12 d-block fw-normal">Inicio:
                                                            {{date('Y-m-d', strtotime($b->created_at))}}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="javascript:void(0)" class="avatar-text avatar-md "
                                                        data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                        <i class="feather feather-more-vertical"></i>
                                                    </a>
                                                    <ul class="dropdown-menu" data-popper-placement="bottom-end">
                                                        <li>
                                                            <a href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $b->cedulaAsegurado }}"
                                                                class="dropdown-item">
                                                                <i class="feather-arrow-right"></i>
                                                                <span>Detalle póliza</span>
                                                            </a>
                                                        </li>
                                                        @candirect('seguros.beneficios.update')
                                                        <li>
                                                            <a href="{{ route('seguros.beneficios.edit', $b->id) }}"
                                                                class="dropdown-item">
                                                                <i class="feather feather-edit-3 me-3"></i>
                                                                <span>Editar</span>
                                                            </a>
                                                        </li>
                                                        @endcandirect
                                                        @candirect('seguros.beneficios.destroy')
                                                        <li>
                                                            <form action="{{ route('seguros.beneficios.destroy', $b->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="opcdestroy" value="individual"
                                                                    id="InputDestroy">
                                                                <button type="submit" class="dropdown-item btnAbrirModalDestroy"
                                                                    data-text="(individual)">
                                                                    <i class="bi bi-person-x-fill"></i>
                                                                    <span>Eliminar Individual</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcandirect
                                                        <li>
                                                            <form action="{{ route('seguros.beneficios.destroy', $b->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="opcdestroy" value="grupo"
                                                                    id="InputDestroy">
                                                                <button type="submit" class="dropdown-item btnAbrirModalDestroy"
                                                                    data-text="(grupal)">
                                                                    <i class="feather feather-trash-2 me-3"></i>
                                                                    <span>Eliminar Grupo</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                        @endcan
                                                    </ul>
                                                </div>
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
            $(document).ready(function () {
                const $todos = $('#planesTODOS');
                const $planes = $('input[name="planes[]"]');

                function actualizarRequerido() {
                    const algunoMarcado = $planes.is(':checked');
                    if (algunoMarcado) {
                        $todos.prop('required', false);
                    } else {
                        $todos.prop('required', true);
                    }
                }
                $todos.on('change', function () {
                    if ($(this).is(':checked')) {
                        $planes.prop('checked', false);
                    }
                    actualizarRequerido();
                });
                $planes.on('change', function () {
                    if ($(this).is(':checked')) {
                        $todos.prop('checked', false);
                    }
                    actualizarRequerido();
                });
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
                $('#inputdesval').on('input', function () {
                    var valor = parseFloat($(this).val().trim());
                    if (!isNaN(valor) && valor > 0) {
                        $('#checkvalbeneficio').slideDown();
                    } else {
                        $('#checkvalbeneficio').slideUp();
                        $('#checkbox2').prop('checked', false);
                    }
                });
                document.querySelectorAll('.btnAbrirModalDestroy').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const formulario = btn.closest('form');
                        let Text = btn.getAttribute('data-text');
                        Swal.fire({
                            title: '¿Está seguro de eliminar el beneficio?',
                            text: `Una vez eliminado, no podrá deshacer este beneficio. ${Text}`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Eliminar',
                            cancelButtonText: 'Cancelar',
                            customClass: {
                                confirmButton: 'btn btn-danger mx-1',
                                cancelButton: 'btn btn-secondary mx-1'
                            },
                            buttonsStyling: false,
                            showClass: {
                                popup: 'animate__animated animate__zoomIn'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__zoomOut'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                formulario.submit();
                            }
                        });
                    });
                });
            });
        </script>
</x-base-layout>