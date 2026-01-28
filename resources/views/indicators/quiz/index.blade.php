<x-base-layout>
    @section('titlepage', 'Quizes')

    <div class="col-xxl-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Participantes del Quiz</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove">
                            </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="card-body custom-card-action">                
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="leadsTab" role="tabpanel">
                        <div id="leads-bar-chart"></div>
                    </div>
                    <div class="tab-pane fade" id="proposalTab" role="tabpanel">
                        <div id="proposal-bar-chart"></div>
                    </div>
                    <div class="tab-pane fade" id="contractTab" role="tabpanel">
                        <div id="contract-bar-chart"></div>
                    </div>
                    <div class="tab-pane fade" id="projectTab" role="tabpanel">
                        <div id="project-bar-chart"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-md-flex flex-wrap p-4 pt-5 border-top border-gray-5">
                <div class="flex-fill mb-4 mb-md-0 pb-2 pb-md-0">
                    <p class="fs-11 fw-semibold text-uppercase text-primary mb-2">Current</p>
                    <h2 class="fs-20 fw-bold mb-0">$65,658 USD</h2>
                </div>
                <div class="vr mx-4 text-gray-600 d-none d-md-flex"></div>
                <div class="flex-fill mb-4 mb-md-0 pb-2 pb-md-0">
                    <p class="fs-11 fw-semibold text-uppercase text-danger mb-2">Overdue</p>
                    <h2 class="fs-20 fw-bold mb-0">$34,54 USD</h2>
                </div>
                <div class="vr mx-4 text-gray-600 d-none d-md-flex"></div>
                <div class="flex-fill">
                    <p class="fs-11 fw-semibold text-uppercase text-success mb-2">Additional</p>
                    <h2 class="fs-20 fw-bold mb-0">$20,478 USD</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- [Sales Pipeline] end -->
    <!-- [Revenue Forecast] start -->
    <div class="col-xxl-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title">Revenue Forecast</h5>
                <div class="card-header-action">
                    <div class="card-header-btn">
                        <div data-bs-toggle="tooltip" title="Delete">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger"
                                data-bs-toggle="remove"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Refresh">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning"
                                data-bs-toggle="refresh"> </a>
                        </div>
                        <div data-bs-toggle="tooltip" title="Maximize/Minimize">
                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success"
                                data-bs-toggle="expand"> </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown"
                            data-bs-offset="25, 25">
                            <div data-bs-toggle="tooltip" title="Options">
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
                                & Tricks</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body custom-card-action">
                <div class="text-center mb-4">
                    <div class="goal-prigress"></div>
                </div>
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-activity"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Marketing Gaol</h2>
                            <div class="fs-11 text-muted">$550/$1250 USD</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-users"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Teams Goal</h2>
                            <div class="fs-11 text-muted">$550/$1250 USD</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-check-circle"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Leads Goal</h2>
                            <div class="fs-11 text-muted">$850/$950 USD</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="px-4 py-3 text-center border border-dashed rounded-3">
                            <div class="avatar-text bg-gray-200 mx-auto mb-2">
                                <i class="feather-dollar-sign"></i>
                            </div>
                            <h2 class="fs-13 tx-spacing-1">Revenue Goal</h2>
                            <div class="fs-11 text-muted">$5,655/$12,500 USD</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="javascript:void(0);" class="btn btn-primary">Generate Report</a>
            </div>
        </div>
    </div>
</x-base-layout>
