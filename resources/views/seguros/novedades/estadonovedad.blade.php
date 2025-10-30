@php
    $tipos = [
        1 => ['color' => 'warning', 'label' => 'MODIFICACION'],
        2 => ['color' => 'success', 'label' => 'INGRESO'],
        3 => ['color' => 'danger', 'label' => 'RETIRO'],
        4 => ['color' => 'info', 'label' => 'INGRESO BENEFICIARIO'],
    ];
    $opcionesestados = [
        1 => 'nuevas',
        2 => 'radicado',
        3 => 'aprobado',
        4 => 'rechazado',
        5 => 'complementos',
    ];
@endphp
<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        @candirect('seguros.poliza.update')
        <a href="{{ route('seguros.novedades.create') }}" class="btn btn-primary w-100">
            <i class="feather-plus me-2"></i>
            <span>Crear Solicitud</span>
        </a>
        @endcandirect
    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'nuevas' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'nuevas']) }}">
                    <i class="feather-edit"></i>
                    <span>Nuevas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'radicado' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'radicado']) }}">
                    <i class="feather-folder"></i>
                    <span>Radicadas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'complementos' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'complementos']) }}">
                    <i class="bi bi-file-earmark-diff"></i>
                    <span>Cargue documentos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'aprobado' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'aprobado']) }}">
                    <i class="feather-check-circle"></i>
                    <span>Aprobado</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'rechazado' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'rechazado']) }}">
                    <i class="bi bi-folder-x"></i>
                    <span>Rechazado</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- [ Content Sidebar  ] end -->
<!-- [ Main Area  ] start -->

<div class="content-area" data-scrollbar-target="#psScrollbarInit">
    <x-success />
    <x-error />
    <div class="content-area-header bg-white sticky-top">
        <div class="page-header-left d-flex align-items-center gap-3">
            <a class="app-sidebar-open-trigger me-2">
                <i class="feather-align-left fs-20"></i>
            </a>
            <div class="custom-control custom-checkbox ms-1 me-2">
                <input type="checkbox" class="custom-control-input" id="checkAll" data-checked-action="show-options">
                <label class="custom-control-label" for="checkAll"></label>
            </div>
            <div class="action-list-items">
                @if ($estado != 'aprobado' && $estado != 'rechazado')
                    <form id="novedadesForm" action="{{ route('seguros.novedades.update', 1) }}" method="POST"
                        class="d-flex align-items-center gap-2 me-3">
                        @csrf
                        @method('PUT')
                        <select name="estado" class="form-select form-select-sm me-2" required>
                            @foreach ($opcionesestados as $valor => $texto)
                                @if ($texto !== $estado)
                                    <option value="{{ $valor }}">{{ strtoupper($texto) }}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="text" name="observaciones" class="form-control form-control-sm uppercase-input"
                            placeholder="Observaciones" required>
                        <button type="submit" class="btn btn-warning">Actualizar Estado</button>
                    </form>
                @endif
            </div>
            <a href="{{ route('seguros.novedades.download') }}" class="btn btn-light-brand">
                <i class="feather-folder-plus me-2"></i>
                <span>Descargar Excel</span>
            </a>
            <h4 class="fw-bolder mb-0">{{ ucfirst($estado) }}</h4>
        </div>
        <div class="page-header-right ms-auto">
            <div class="hstack gap-2">
                <div class="hstack">
                    <a href="javascript:void(0)" class="search-form-open-toggle">
                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="Search">
                            <i class="feather-search"></i>
                        </div>
                    </a>
                    <form class="search-form" style="display: none">
                        <div class="search-form-inner">
                            <a href="javascript:void(0)" class="search-form-close-toggle">
                                <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    title="Back">
                                    <i class="feather-arrow-left"></i>
                                </div>
                            </a>
                            <input type="search" class="py-3 px-0 border-0 w-100" id="emailSearch"
                                placeholder="Buscar..." autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="dropdown d-none d-sm-flex">
                    <a href="javascript:void(0)" class="btn btn-light-brand btn-sm rounded-pill dropdown-toggle"
                        data-bs-toggle="dropdown" data-bs-offset="0,23" aria-expanded="false">1-15 of 762 </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)">Oldest</a></li>
                    </ul>
                </div>
                <div class="hstack d-none d-sm-flex">
                    <a href="javascript:void(0)" class="d-flex me-1">
                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="Previous">
                            <i class="feather-chevron-left"></i>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="d-flex me-1">
                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="Next">
                            <i class="feather-chevron-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="content-area-body p-0">
        @foreach ($data as $index => $reg)
            <div class="single-items">
                <!--! [item-meta] !-->
                <div class="d-flex wd-80 gap-4 ms-1 item-meta">
                    <div class="item-checkbox">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checkbox" id="{{ $index + 1 }}bcb"
                                data-checked-action="show-options" name="ids[]" value="{{ $reg->id }}"
                                form="novedadesForm">
                            <label class="custom-control-label" for="{{ $index + 1 }}bcb"></label>
                        </div>
                    </div>
                </div>
                <!--! [item-info] !-->
                <div class="d-flex align-items-start gap-4 w-100 item-info" data-view-toggle="details">
                    <a href="{{ route('seguros.poliza.show', ['poliza' => 'ID']) . '?id=' . $reg->id_asegurado }}"
                        class="hstack gap-3" style="width: 30%;">
                        <i class="bi bi-person-circle"></i>
                        <div>
                            <span class="text-truncate-1-line">{{ $reg->id_asegurado }}</span>
                            <small class="fs-12 fw-normal text-muted">{{ $reg->nombre_tercero ?? '' }}</small>
                        </div>
                    </a>
                    @php
                        $tipo = $tipos[$reg->tipo] ?? ['color' => 'secondary', 'label' => 'SIN DEFINIR'];
                    @endphp
                    <span class="badge bg-soft-{{ $tipo['color'] }} text-{{ $tipo['color'] }}"
                        style="width: 15%;">{{ $tipo['label'] }}</span>
                    <div style="width: 10%;">
                        @if ($reg->beneficiario)
                            {{ $reg->beneficiario->nombre ?? '' }}
                        @else
                            <div class="fw-semibold text-dark">$
                                {{ number_format($reg->valorAsegurado) }}</div>
                            <div class="fs-12 text-muted">$
                                {{ number_format($reg->primaAseguradora) }}
                            </div>
                        @endif
                    </div>
                    <a class="d-none d-md-block" style="width: 45%;">
                        <div class="w-100 text-truncate-1-line item-desc">
                            <span class="ms-3"> {{ $reg->cambiosEstado->last()->observaciones ?? '' }}</span>
                        </div>
                    </a>
                </div>
                <!--! [item-date] !-->
                <div class="d-flex align-items-center justify-content-end wd-150 gap-3 item-data">
                    <div class="fs-11 fw-medium text-muted text-uppercase d-none d-sm-block item-time">
                        {{ optional($reg->cambiosEstado->last())->fechaCierre
                            ? optional($reg->cambiosEstado->last()->fechaCierre)->translatedFormat('d M Y')
                            : $reg->created_at->translatedFormat('d M Y') }}
                    </div>
                    <div class="item-action">
                        <div class="dropdown">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-offset="0, 28">
                                <div class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    title="Más Opciones">
                                    <i class="feather-more-vertical"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('seguros.novedades.edit', $reg->id) }}" class="dropdown-item"
                                    data-view-toggle="details">
                                    <i class="feather-eye me-3"></i>
                                    <span>Editar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{-- <div class="p-4 bg-white d-flex align-items-center justify-content-center justify-content-md-between">
            <div class="content-sidebar-footer wd-300 d-none d-md-block">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h2 class="fs-11 tx-spacing-1 mb-0">Storage</h2>
                    <div class="fs-11 text-muted">43.5GB used of <span class="fw-bold text-dark">100GB</span></div>
                </div>
                <div class="progress ht-3">
                    <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="43" aria-valuemin="0"
                        aria-valuemax="100" style="width: 43%"></div>
                </div>
            </div>
            <div class="hstack gap-2 fs-11">
                <a href="javascript:void(0);">Terms</a>
                <span class="wd-3 ht-3 bg-gray-500 rounded-circle"></span>
                <a href="javascript:void(0);">Privacy</a>
                <span class="wd-3 ht-3 bg-gray-500 rounded-circle"></span>
                <a href="javascript:void(0);">Policies</a>
            </div>
        </div> --}}
    </div>
    <script>
        $('#novedadesForm').on('submit', function(event) {
            event.preventDefault();
            let observaciones = $('input[name="observaciones"]').val();
            let estado = $('select[name="estado"]').val();
            if (observaciones.trim() === "" || estado.trim() === "") {
                alert("Todos los campos son obligatorios");
                return;
            }
            this.submit();
        });
        $(document).on('click', '.item-info', function(event) {
            event.preventDefault();
            event.stopPropagation();
        });
    </script>
</div>
