<x-base-layout>
    @section('titlepage', 'Convenio')
    <x-success />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body task-header d-lg-flex align-items-center justify-content-between">
                <div class="mb-4 mb-lg-0">
                    <div class="fs-12 text-muted">Fecha de inicio: {{ $convenio->fecha_inicio }}. Fecha fin:
                        {{ $convenio->fecha_fin }} </div>
                    <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $convenio->nombre }}</span>@if ($convenio->vigente)
                    <a class="badge bg-soft-success text-success ms-1">Vigente</a>
                @endif</div>
                    <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $convenio->idAseguradora }}</h3>
                </div>                
                <div class="d-flex gap-2">
                    @candirect('seguros.convenio.store')
                    <a href="{{ route('seguros.convenio.create', ['id' => $convenio->id]) }}"
                        class="btn btn-light-brand">Copiar Convenio</a>
                    @endcandirect
                    @candirect('seguros.planes.store')
                    <a href="{{ route('seguros.planes.create') }}" class="btn btn-success" data-bs-toggle="tooltip"
                        title="" data-bs-original-title="{{ $convenio->nombre }}">
                        <i class="feather-plus me-2"></i>
                        <span>Agregar Plan</span>
                    </a>
                    @endcandirect
                </div>
            </div>
        </div>
    </div>
    @foreach ($planes as $condicionId => $planesGrupo)
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="hstack justify-content-between flex-wrap flex-md-nowrap mb-4">
                        <div>
                            <h5 class="mb-1">{{ $planesGrupo->first()->condicioncorpen->descripcion }}</h5>
                        </div>                        
                    </div>
                    <div class="row justify-content-center">
                        @php
                            $colors = ['text-warning', 'text-success', 'text-primary', 'text-teal', 'text-danger'];
                        @endphp
                        @foreach ($planesGrupo as $plan)
                            <div class="col-xxl-2 col-lg-4 col-md-6">
                                <div class="card stretch stretch-full border border-dashed border-gray-5">
                                    <div class="card-body rounded-3">
                                        <div class="dropdown open text-end">
                                            <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                                aria-expanded="false" class="">
                                                <i class="feather-more-vertical"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" style="">
                                                @candirect('seguros.planes.update')
                                                <a class="dropdown-item"
                                                    href="{{ route('seguros.planes.edit', ['plan' => $plan->id]) }}">Editar</a>
                                                @endcandirect
                                                @candirect('seguros.planes.destroy')
                                                <form
                                                    action="{{ route('seguros.planes.destroy', ['plan' => $plan->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item btnAbrirModalDestroy"
                                                        data-text="plan ">Eliminar</button>
                                                </form>
                                                @endcandirect
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="mt-2 {{ $colors[$plan->id % count($colors)] }}">
                                                {{ $plan->name }}
                                            </h6>
                                            <div class=" fw-bolder text-dark mt-3 mb-1" style="font-size:15px;">
                                                ${{ number_format($plan->valor) }}</div>
                                            <p
                                                class="fs-12 fw-medium text-muted text-spacing-1 mb-0 text-truncate-1-line">
                                                <span class="fw-semibold text-dark">Prima:</span>
                                                ${{ number_format($plan->prima_aseguradora) }}
                                            </p>
                                            <p
                                                class="fs-12 fw-medium text-muted text-spacing-1 mb-0 text-truncate-1-line">
                                                <span class="fw-semibold text-dark">Prima Asegurado:</span>
                                                ${{ number_format($plan->prima_asegurado) }}
                                            </p>
                                            <p
                                                class="fs-12 fw-medium text-muted text-spacing-1 mb-0 text-truncate-1-line">
                                                <span class="fw-semibold text-dark">Prima Pastor:</span>
                                                ${{ number_format($plan->prima_pastor) }}
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</x-base-layout>
<script>
    $(document).ready(function() {
        $('.btnEliminar').click(function(event) {
            event.preventDefault();
            formToSubmit = $(this).closest('form');
            $('#ModalConfirmacionEliminar').modal('show');
            $('#botonSiModal').off('click').on('click', function() {
                if (formToSubmit) {
                    formToSubmit.off('submit');
                    formToSubmit.submit();
                }
            });
        });
    });
</script>
