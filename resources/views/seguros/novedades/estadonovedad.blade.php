@php
    $tipos = [
        1 => ['color' => 'warning', 'label' => 'MODIFICACION'],
        2 => ['color' => 'success', 'label' => 'INGRESO'],
        3 => ['color' => 'danger', 'label' => 'RETIRO'],
        4 => ['color' => 'info', 'label' => 'INGRESO BENEFICIARIO'],
    ];
@endphp

<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <a href="{{ route('seguros.novedades.create') }}" class="btn btn-primary w-100">
            <i class="feather-plus me-2"></i>
            <span>Crear Solicitud</span>
        </a>
    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'solicitud' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'solicitud']) }}">
                    <i class="feather-edit"></i>
                    <span>Solicitadas</span>
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
                <a class="nav-link {{ request('estado') === 'aprobado' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'aprobado']) }}">
                    <i class="feather-check-circle"></i>
                    <span>Aprobadas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('estado') === 'rechazado' ? 'active' : '' }}"
                    href="{{ route('seguros.novedades.index', ['estado' => 'rechazado']) }}">
                    <i class="bi bi-folder-x"></i>
                    <span>Rechazadas</span>
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
                <div class="dropdown">
                    <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown"
                        data-bs-offset="0,22">
                        <i class="feather-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-eye me-3"></i>
                                <span>Read</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-eye-off me-3"></i>
                                <span>Unread</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-star me-3"></i>
                                <span>Starred</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-shield-off me-3"></i>
                                <span>Unstarred</span>
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-clock me-3"></i>
                                <span>Snooze</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-check-circle me-3"></i>
                                <span>Add Tasks</span>
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-archive me-3"></i>
                                <span>Archive</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-alert-octagon me-3"></i>
                                <span>Report Spam</span>
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="feather-trash-2 me-3"></i>
                                <span>Delete</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="d-flex" data-bs-toggle="dropdown" data-bs-offset="0,22"
                        data-bs-auto-close="outside" aria-expanded="false">
                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="Tags">
                            <i class="feather-tag"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Office" checked="checked">
                                <label class="custom-control-label c-pointer" for="Office">Office</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Family">
                                <label class="custom-control-label c-pointer" for="Family">Family</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Friend"
                                    checked="checked">
                                <label class="custom-control-label c-pointer" for="Friend">Friend</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Marketplace">
                                <label class="custom-control-label c-pointer" for="Marketplace"> Marketplace </label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Development">
                                <label class="custom-control-label c-pointer" for="Development"> Development </label>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-plus me-3"></i>
                            <span>Create Tag</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-tag me-3"></i>
                            <span>Manages Tag</span>
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="d-flex" data-bs-toggle="dropdown" data-bs-offset="0,22"
                        data-bs-auto-close="outside" aria-expanded="false">
                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="Labels">
                            <i class="feather-folder-plus"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu">
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Updates">
                                <label class="custom-control-label c-pointer" for="Updates">Updates</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Socials">
                                <label class="custom-control-label c-pointer" for="Socials">Socials</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Primary"
                                    checked="checked">
                                <label class="custom-control-label c-pointer" for="Primary">Primary</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Forums">
                                <label class="custom-control-label c-pointer" for="Forums">Forums</label>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="Promotions"
                                    checked="checked">
                                <label class="custom-control-label c-pointer" for="Promotions"> Promotions </label>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-plus me-3"></i>
                            <span>Create Label</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-folder-plus me-3"></i>
                            <span>Manages Label</span>
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="d-flex" data-bs-toggle="dropdown" data-bs-offset="0,22"
                        data-bs-auto-close="outside" aria-expanded="false">
                        <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="More Options">
                            <i class="feather-more-vertical"></i>
                        </div>
                    </a>
                    <div class="dropdown-menu">
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-plus me-3"></i>
                            <span>Add to Group</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-user-plus me-3"></i>
                            <span>Add to Contact</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-eye-off me-3"></i>
                            <span>Make as Unread</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-sliders me-3"></i>
                            <span>Filter Messages</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-archive me-3"></i>
                            <span>Make as Archive</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-slash me-3"></i>
                            <span>Report Spam</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-sliders me-3"></i>
                            <span>Report phishing</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-download me-3"></i>
                            <span>Download Messages</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-bell-off me-3"></i>
                            <span>Mute Conversion</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-slash me-3"></i>
                            <span>Block Conversion</span>
                        </a>
                        <a href="javascript:void(0)" class="dropdown-item">
                            <i class="feather-trash-2 me-3"></i>
                            <span>Delete Conversion</span>
                        </a>
                    </div>
                </div>
            </div>
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
                                placeholder="Search..." autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="dropdown d-none d-sm-flex">
                    <a href="javascript:void(0)" class="btn btn-light-brand btn-sm rounded-pill dropdown-toggle"
                        data-bs-toggle="dropdown" data-bs-offset="0,23" aria-expanded="false">1-15 of 762 </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)">Oldest</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)">Newest</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)">Replied</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="javascript:void(0)">Ascending</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)">Descending</a></li>
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
                            <input type="checkbox" class="custom-control-input checkbox"
                                id="checkBox_{{ $index }}" data-checked-action="show-options">
                            <label class="custom-control-label" for="checkBox_{{ $index }}"></label>
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
                    <div class="fs-11 fw-medium text-muted text-uppercase d-none d-sm-block item-time">{{ $reg->created_at->translatedFormat('d M Y') }}</div>
                    <div class="item-action">
                        <div class="dropdown">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-offset="0, 28">
                                <div class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    title="More Options">
                                    <i class="feather-more-vertical"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item" data-view-toggle="details">
                                    <i class="feather-eye me-3"></i>
                                    <span>View</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-corner-up-right me-3"></i>
                                    <span>Reply</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-fast-forward me-3"></i>
                                    <span>Forward</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-repeat me-3"></i>
                                    <span>Reply All</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0)" class="dropdown-item"
                                    data-action-target="#mailDeleteMessage">
                                    <i class="feather-x me-3"></i>
                                    <span>Delete</span>
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
</div>
