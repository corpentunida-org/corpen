@php
    $colors = ['success', 'info', 'secondary', 'warning', 'primary'];
@endphp
<x-base-layout>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div class="recent-activity p-4 pb-0">
                    <div class="mb-4 pb-2 d-flex justify-content-between">
                        <h5 class="fw-bold">Convenios:</h5>
                        <a href="javascript:void(0);" class="btn btn-sm btn-light-brand">View Alls</a>
                    </div>
                    <ul class="list-unstyled activity-feed">
                        @foreach ($convenios as $i => $convenio)
                            <li
                                class="d-flex justify-content-between feed-item feed-item-{{ $colors[$i % count($colors)] }}">
                                <div>
                                    <span class="text-truncate-1-line lead_date">Fecha de inicio
                                        {{ $convenio->fecha_inicio }} A fecha fin {{ $convenio->fecha_fin }}</span>
                                    <span class="text">
                                        Contrato {{ substr($convenio->nombre, 0, -4) }}
                                        <a href="javascript:void(0);"
                                            class="fw-bold text-{{ $colors[$i % count($colors)] }}">{{ substr($convenio->nombre, -4) }}</a>
                                    </span>
                                </div>
                                <div class="ms-3 d-flex gap-2 align-items-center">                                
                                    <a href="{{route('seguros.convenio.show', ['convenio' => $convenio->id] )}}" class="avatar-text avatar-sm" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" title="" data-bs-original-title="Copiar convenio"
                                        aria-label="Copiar convenio"><i class="bi bi-clipboard"></i></a>
                                    <a href="" class="avatar-text avatar-sm" data-bs-toggle="tooltip"
                                        data-bs-trigger="hover" title="" data-bs-original-title="Ver Detalle"
                                        aria-label="Ver Detalle"><i class="feather feather-eye fs-12"></i></a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
