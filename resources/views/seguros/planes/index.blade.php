<x-base-layout>
    @section('titlepage', 'Planes')

    @foreach ($planes as $condicionId => $planesGrupo)
        {{-- <div class="mb-4 w-100">
        <h4 class="text-primary mb-3">
            {{ $planesGrupo->first()->condicion->descripcion }}
        </h4>

        <div class="d-flex flex-nowrap gap-3 overflow-auto">
        @php
            $tarjeta = 1;
        @endphp
            @foreach ($planesGrupo as $plan)
                @if ($tarjeta % 2 != 0)
                    <div class="card flex-fill">
                @else                    
                    <div class="card flex-fill bg-soft-100">
                @endif
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="avatar-text">
                                <h3 class="mt-2">{{ $tarjeta }}</h3>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $plan->name }}</div>
                            </div>                            
                        </div>
                        <div class="dropdown open">
                                <a href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <i class="feather-more-horizontal"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="javascript:void(0);">Editar</a>
                                </div>
                            </div>
                    </div>                    
                    <div class="card-body">
                        <div class="mt-2">
                            <div class="hstack justify-content-between">
                                <div class="fs-4 text-dark">${{ $plan->valor }}</div>
                            </div>
                            <div class="progress ht-5 my-2">                                
                                <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="78" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 78%"></div>
                            </div>
                            <div class="fs-12 text-muted">Valor de prima: <span
                                    class="fw-bold text-dark">${{ $plan->prima }}</span>
                            </div>
                        </div>
                    </div>
                </div>                
                @php
                    $tarjeta++; 
                @endphp
                </div>
            @endforeach
        </div>

        <hr class="border-top-dashed my-2 mx-3">
    </div> --}}
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
