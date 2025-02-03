<x-base-layout>
    @section('titlepage', 'Administrador')
    <x-success />
    <div class="col-xxl-4 col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Proyectos</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand">
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="" data-bs-original-title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-at-sign"></i>New</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-calendar"></i>Event</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-bell"></i>Snoozed</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-trash-2"></i>Deleted</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-settings"></i>Settings</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Tips
                                &amp;
                                Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action">
                <div class="mb-3">
                    <div class="mb-4 pb-1 d-flex">
                        <div class="d-flex w-50 align-items-center me-3">
                            <div class="wd-40 ht-40 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date me-3">
                                <span class="fs-18 fw-bold mb-1 d-block">1</span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="text-truncate-1-line">Exequiales</a>
                                <div class="fs-11 text-muted">App UI Kit</div>
                            </div>
                        </div>
                        <div class="d-flex flex-grow-1 align-items-center">
                            <div class="progress w-100 me-3 ht-5">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 86%"
                                    aria-valuenow="86" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted">86%</span>
                        </div>
                    </div>
                    <hr class="border-dashed my-3">
                    <div class="mb-4 pb-1 d-flex">
                        <div class="d-flex w-50 align-items-center me-3">
                            <div class="wd-40 ht-40 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date me-3">
                                <span class="fs-18 fw-bold mb-1 d-block">2</span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="text-truncate-1-line">Creditos</a>
                                <div class="fs-11 text-muted">Marketing</div>
                            </div>
                        </div>
                        <div class="d-flex flex-grow-1 align-items-center">
                            <div class="progress w-100 me-3 ht-5">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 18%"
                                    aria-valuenow="18" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted">18%</span>
                        </div>
                    </div>
                    <hr class="border-dashed my-3">
                    <div class="mb-4 pb-1 d-flex">
                        <div class="d-flex w-50 align-items-center me-3">
                            <div class="wd-40 ht-40 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date me-3">
                                <span class="fs-18 fw-bold mb-1 d-block">3</span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="text-truncate-1-line">CINCO</a>
                                <div class="fs-11 text-muted">Dashboard</div>
                            </div>
                        </div>
                        <div class="d-flex flex-grow-1 align-items-center">
                            <div class="progress w-100 me-3 ht-5">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 90%"
                                    aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted">90%</span>
                        </div>
                    </div>
                    <hr class="border-dashed my-3">
                    <div class="d-flex">
                        <div class="d-flex w-50 align-items-center me-3">
                            <div class="wd-40 ht-40 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date me-3">
                                <span class="fs-18 fw-bold mb-1 d-block">4</span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="text-truncate-1-line">Cartera</a>
                                <div class="fs-11 text-muted">Payment</div>
                            </div>
                        </div>
                        <div class="d-flex flex-grow-1 align-items-center">
                            <div class="progress w-100 me-3 ht-5">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 29%"
                                    aria-valuenow="29" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted">29%</span>
                        </div>
                    </div>
                    <hr class="border-dashed my-3">
                    <div class="d-flex">
                        <div class="d-flex w-50 align-items-center me-3">
                            <div class="wd-40 ht-40 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date me-3">
                                <span class="fs-18 fw-bold mb-1 d-block">5</span>
                            </div>
                            <div>
                                <a href="javascript:void(0);" class="text-truncate-1-line">Seguros</a>
                                <div class="fs-11 text-muted">Payment</div>
                            </div>
                        </div>
                        <div class="d-flex flex-grow-1 align-items-center">
                            <div class="progress w-100 me-3 ht-5">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 76%"
                                    aria-valuenow="76" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted">76%</span>
                        </div>
                    </div>
                </div>
            </div>
            <a href="javascript:void(0);" class="card-footer fs-11 fw-bold text-uppercase text-center"></a>
        </div>
    </div>

    <div class="col-xxl-4 col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Usuarios</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand">
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="" data-bs-original-title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-at-sign"></i>New</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-calendar"></i>Event</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-bell"></i>Snoozed</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-trash-2"></i>Deleted</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-settings"></i>Settings</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Tips
                                &amp;
                                Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action">
                @foreach ($users as $user)
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar-image me-3">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div>
                                <a href="javascript:void(0)"
                                    class="d-flex align-items-center mb-1">{{ $user->name }}</a>
                                <div class="fs-12 fw-normal text-muted">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="dropdown hstack text-end justify-content-end">
                            <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="feather feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="">
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather feather-eye me-3"></i>
                                        <span>Open</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather feather-share-2 me-3"></i>
                                        <span>Share</span>
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather feather-scissors me-3"></i>
                                        <span>Backup</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather feather-x me-3"></i>
                                        <span>Delete</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr class="border-dashed my-3">
                @endforeach
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="card-footer fs-11 fw-bold text-uppercase text-center">Añadir Usuario</a>
        </div>
    </div>

    <div class="col-xxl-4 col-lg-6">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Progress</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="" data-bs-original-title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand">
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="" data-bs-original-title="Options">
                                <i class="feather-more-vertical"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-at-sign"></i>New</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-calendar"></i>Event</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-bell"></i>Snoozed</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-trash-2"></i>Deleted</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item"><i
                                    class="feather-settings"></i>Settings</a>
                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Tips
                                &amp;
                                Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action">
                

            </div>
            <a href="javascript:void(0);" class="card-footer fs-11 fw-bold text-uppercase text-center"></a>
        </div>
    </div>
</x-base-layout>
