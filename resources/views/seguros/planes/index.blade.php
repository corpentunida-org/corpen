<x-base-layout>
    @section('titlepage', 'Planes')

    @foreach ($planes as $condicionId => $planesGrupo)
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="hstack justify-content-between mb-4 pb-">
                        <div>
                            <h5 class="mb-1">{{ $planesGrupo->first()->condicion->descripcion }}</h5>
                            <span class="fs-12 text-muted">Seguros de Vida</span>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-light-brand">View Alls</a>
                    </div>
                    <div class="row justify-content-center">
                        @php
                            $colors = ['text-warning', 'text-success', 'text-primary', 'text-teal', 'text-danger'];
                        @endphp
                        @foreach ($planesGrupo as $plan)
                            <div class="col-xxl-2 col-lg-4 col-md-6">
                                <div class="card stretch stretch-full border border-dashed border-gray-5">
                                    <div class="card-body rounded-3 text-center">
                                        {{-- <i class="bi bi-envelope-plus fs-3 text-warning"></i> --}}
                                        <h6 class="mt-2 {{ $colors[$plan->id % count($colors)] }}">{{ $plan->name }}</h6>
                                        <div class="fs-4 fw-bolder text-dark mt-3 mb-1">$
                                            {{ number_format($plan->valor) }}</div>
                                        <p class="fs-12 fw-medium text-muted text-spacing-1 mb-0 text-truncate-1-line">
                                            Prima: ${{ number_format($plan->prima) }}</p>
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
