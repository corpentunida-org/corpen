<x-base-layout>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">{{$convenio->nombre}}</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="card-body custom-card-action p-0">
                <div class="table-responsive">
                    @foreach ($convenio->plan->groupBy('condicion.id') as $condicionId => $planes)

                    @if ($condicionId == 0)
                        <div class="border-bottom text-center p-3">
                    @else
                        <div class="border border-start-0 border text-center p-3">
                    @endif
                    
                        @php
                            $condicion = $planes->first()->condicion;
                        @endphp
                        {{ $condicion->descripcion }}
                    </div>
                
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Valor Asegurado</th>
                                <th>Prima</th>                                
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($planes as $plan)                       
                            <tr>
                                <td>
                                    <div class="hstack gap-3">                                        
                                        <div>
                                            <a href="javascript:void(0);" class="d-block">{{$plan->name}}</a>
                                            <span class="fs-12 text-muted">PLAN ID </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="d-block mb-1">{{$plan->valor}}</a>
                                    <span class="fs-12 text-muted d-block">VALOR COBERTURA MÁS ALTO</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="d-block mb-1">{{$plan->prima}}</a>
                                    <span class="fs-12 text-muted d-block">PRIMA MENSUAL POR ASEGURADO </span>
                                </td>                                
                            </tr> 
                             @endforeach                           
                        </tbody>
                    </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>   
</x-base-layout>