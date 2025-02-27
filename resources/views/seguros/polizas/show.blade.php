<x-base-layout>
    @section('titlepage', 'Polizas')
    <x-success />
    <x-error />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="d-flex gap-4 align-items-center">
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="fs-12 text-muted">Titular: </div>
                            <div class="fs-4 fw-bold text-dark"><span
                                    class="counter">{{ $poliza->asegurado->terceroAF->nombre }}</span></div>
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $poliza->asegurado->titular }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                    <x-input-search-seguros></x-input-search-seguros>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="subscription-plan px-4 pt-4 mb-3">
            <div class="mb-4 d-flex align-items-center justify-content-between gap-4">
                <div class="mb-4 d-flex align-items-center justify-content-start gap-4">
                    <div class="btn btn-sm btn-light-brand p-3 bg-soft-primary">{{ $poliza->asegurado->parentesco }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $poliza->tercero->nombre }}:</h5>
                        <div class="fs-12 text-muted">{{ $poliza->seg_asegurado_id }}</div>
                    </div>
                </div>
                @if ($poliza->reclamacion != 0)
                    <a href="{{ route('seguros.reclamacion.edit', ['reclamacion' => $poliza->esreclamacion->id]) }}" class="btn btn-sm bg-soft-danger text-danger">Activo en una reclamación</a>
                @endif
            </div>
            <div class="p-4 mb-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-between gap-4 border border-dashed border-gray-5 rounded-1">
                <div>
                    <div class="fs-14 fw-bold text-dark mb-1">Seguro de vida
                        <a href="javascript:void(0);"class="badge bg-primary text-white ms-2">{{ $poliza->plan->name }}</a>
                    </div>
                    <div class="fs-12 text-muted">{{ $poliza->plan->condicion->descripcion }}</div>
                </div>
                <div class="my-3 my-xxl-0 my-md-3 my-md-0">
                    <div class="fs-20 text-dark"><span class="fw-bold">$
                            {{ number_format($poliza->valor_prima) }}</span> / <em class="fs-11 fw-medium">Mes</em>
                    </div>
                    <div class="fs-12 text-muted mt-1"><strong class="text-dark">EXTRA:
                        </strong>{{ $poliza->extra_prima }}%</div>
                </div>
                <div class="hstack gap-3">
                    @if ($poliza->reclamacion == 0)
                    <a href="{{ route('seguros.reclamacion.create', ['a' => $poliza->seg_asegurado_id]) }}" class="text-danger">Generar Reclamación</a>
                    @else
                        <a class="text-danger">{{ $poliza->esreclamacion->estadoReclamacion->nombre }}</a>
                    @endif
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center" style="height: auto;">
                @foreach ($poliza->plan->coberturas as $cobertura)
                    <div class="d-flex flex-column align-items-center justify-content-between text-center p-4 mb-4 bg-soft-100 border border-dashed border-gray-5 rounded-1 position-relative"
                        style="flex: 1 1 calc(100% / 7); max-width: calc(100% / 7); min-width: 150px; margin: 5px;">
                        <div style="height: 60%;">
                            <h6 class="fs-13 fw-bold">{{ $cobertura->nombre }}</h6>
                            <p class="fs-12 fw-normal text-muted">Seguros de vida</p>
                        </div>
                        <div class="mt-4">
                            <span class="fs-16 fw-bold text-dark">$
                                {{ number_format($cobertura->pivot->valorAsegurado) }}</span>
                            <p class="fs-12 fw-normal text-muted">Valor asegurado</p>
                        </div>
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <i class="feather-check fs-12 bg-primary text-white p-1 rounded-circle"></i>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        @if ($grupoFamiliar->count() >= 1)
            <div class="payment-history">
                <div class="mb-4 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">Grupo Familiar:</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="border-top">
                                <th>Cedula</th>
                                <th>Nombre</th>
                                <th>Parentesco</th>
                                <th>Plan</th>
                                <th>Valor Asegurado</th>
                                <th>Prima</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($grupoFamiliar as $familiar)
                                <tr>
                                    <td><a href="javascript:void(0);">{{ $familiar->cedula }}</a></td>
                                    <td>{{ $familiar->tercero->nombre }}</td>
                                    <td>{{ $familiar->parentesco }}</td>
                                    <td><span
                                            class="badge bg-soft-warning text-warning">{{ $familiar->polizas->first()->plan->name }}</span>
                                    </td>
                                    <td>$ {{ number_format($familiar->valorAsegurado) }}</td>
                                    <td>$ {{ number_format($familiar->valorprima) }}</td>
                                    <td class="hstack justify-content-end gap-4 text-end">
                                        <form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}"
                                            method="GET">
                                            <div data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                data-bs-original-title="Ver Poliza">
                                                <input type="hidden" name="id" id="valueCedula"
                                                    value="{{ $familiar->cedula }}">
                                                <button class="w-50 btn">
                                                    <i class="feather feather-send fs-12"></i>
                                                    <span></span>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 mb-4 d-xxl-flex d-xl-block d-md-flex align-items-center justify-content-end gap-4">
                    @if ($poliza->asegurado->parentesco == 'AF')
                        <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                            <a href="javascript:void(0);" class="text-bold">Valor Titular</a>
                            <a href="javascript:void(0);" class="btn bg-soft-info" style="font-size:20px;">$
                                {{ number_format($poliza->asegurado->valorpAseguradora) }}</a>
                        </div>
                        <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                            <a href="javascript:void(0);" class="text-bold">Subsidio</a>
                            <a href="javascript:void(0);" class="btn bg-soft-warning" style="font-size:20px;">$
                                @if (!$poliza->asegurado->valorpAseguradora)
                                    0
                                @else
                                    @php $subsicio = $totalPrima - $poliza->asegurado->valorpAseguradora @endphp {{ number_format($subsicio) }}
                                @endif
                            </a>
                        </div>
                    @endif
                    <div class="d-flex gap-4 align-items-center justify-content-sm-end justify-content-between">
                        <a href="javascript:void(0);" class="text-bold">Valor Aseguradora</a>
                        <a href="javascript:void(0);" class="btn bg-soft-primary" style="font-size:20px;">$
                            {{ number_format($totalPrima) }}</a>
                    </div>
                </div>
            </div>
        @endif

        @if ($beneficiarios->isnotEmpty())
            @include('seguros.beneficiarios.show')
        @endif
        <div class="my-4 d-flex align-items-center justify-content-start">
            <div class="d-flex gap-2">
                <a href="{{ route('seguros.beneficiario.create', ['a' => $poliza->seg_asegurado_id, 'p' => $poliza->id]) }}"
                    class="btn btn-success">
                    <i class="feather-plus me-2"></i>
                    <span>Agregar Beneficiario</span>
                </a>
            </div>
        </div>
    </div>
</x-base-layout>
