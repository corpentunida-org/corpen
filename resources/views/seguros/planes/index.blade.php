<x-base-layout>
@section('titlepage', 'Planes')

@foreach ($planes as $condicionId => $planesGrupo)
    <div class="mb-4 w-100">
        {{-- Mostrar la descripción de la condición --}}
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
                                <!-- <i class="feather feather-star"></i> -->
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
                                <!-- <div class="badge bg-soft-danger text-danger">
                                    <span>20.9%</span>
                                    <i class="feather-chevron-down fs-12 ms-1"></i>
                                </div> -->
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
    </div>
@endforeach


</x-base-layout>