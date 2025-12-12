<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Corpentunida') }}</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo/corpenfavicon.png') }}" />
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tagify.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tagify-data.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/quill.min.css') }}">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
   <script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
   <script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
    <style>
        .uppercase-input {
            text-transform: uppercase;
        }
    </style>
    <!--! ================================================================ !-->
    <!--! [Start] Navigation Manu !-->
    <!--! ================================================================ !-->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <a href="{{ route('dashboard') }}" class="b-brand d-flex justify-content-center align-items-center">
                <!-- ========   change your logo here   ============ -->
                <img src="https://www.fecp.org.co/images/CORPENTUNIDA_LOGO_PRINCIPAL.png" alt=""
                    class="logo logo-sm" width="200px" />
            </a>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Menu</label>
                    </li>
                    @foreach (auth()->user()->actions as $action)
                        @include('layouts.actions.' . $action->role->name)
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
    <!--! ================================================================ !-->
    <!--! [End]  Navigation Manu !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
                <div class="nxl-drp-link nxl-lavel-mega-menu">
                    <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                        <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    <div class="nxl-head-link dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>
                    <x-notifications-base />
                    <div class="nxl-h-item d-none d-sm-flex">
                        <div class="full-screen-switcher">
                            <a href="javascript:void(0);" class="nxl-head-link me-0"
                                onclick="$('body').fullScreenHelper('toggle');">
                                <i class="feather-maximize maximize"></i>
                                <i class="feather-minimize minimize"></i>
                            </a>
                        </div>
                    </div>
                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button"
                            data-bs-auto-close="outside">
                            <img src="{{ asset('assets/images/avatar/1.png') }}" alt="user-image"
                                class="img-fluid user-avtar me-0" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <!-- <img src="" alt="user-image" class="img-fluid user-avtar" /> -->
                                    <div>
                                        <h6 class="text-dark mb-0">{{ auth()->user()->name }}</h6>
                                        <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('profile.show') }}" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>Detalle Perfil</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="feather-log-out"></i>{{ __('Cerrar Sesión') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!--! ================================================================ !-->
    <!--! [End] Header !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <main class="nxl-container apps-container apps-email">
        <div class="nxl-content without-header nxl-full-content">
            <!-- [ Main Content ] start -->
            <div class="main-content d-flex">
                <!-- [ Content Sidebar ] start -->
                {{-- <div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
                    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
                        <h4 class="fw-bolder mb-0">Email</h4>
                        <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex">
                            <i class="feather-x"></i>
                        </a>
                    </div>
                    <div class="content-sidebar-header">
                        <a href="javascript:void(0);" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#composeMail">
                            <i class="feather-plus me-2"></i>
                            <span>Compose</span>
                        </a>
                    </div>
                    <div class="content-sidebar-body">
                        <ul class="nav flex-column nxl-content-sidebar-item">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-between" href="javascript:void(0);">
                                    <span class="d-flex align-items-center">
                                        <i class="feather-inbox me-3"></i>
                                        <span>Inbox</span>
                                    </span>
                                    <span class="badge bg-soft-primary text-primary">5</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <i class="feather-send"></i>
                                    <span>Send</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <i class="feather-edit"></i>
                                    <span>Draft</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-between" href="javascript:void(0);">
                                    <span class="d-flex align-items-center">
                                        <i class="feather-inbox me-3"></i>
                                        <span>Spam</span>
                                    </span>
                                    <span class="badge bg-soft-danger text-danger">7</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <i class="feather-trash-2"></i>
                                    <span>Delete</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav flex-column nxl-content-sidebar-item">
                            <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1 d-flex align-items-center justify-content-between">
                                <span>Label</span>
                                <a href="javascript:void(0);">
                                    <span class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Add New"> <i class="feather-plus"></i> </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <span class="wd-7 ht-7 bg-primary rounded-circle"></span>
                                    <span>Work</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <span class="wd-7 ht-7 bg-warning rounded-circle"></span>
                                    <span>Partnership</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <span class="wd-7 ht-7 bg-teal rounded-circle"></span>
                                    <span>In Progress</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <span class="wd-7 ht-7 bg-danger rounded-circle"></span>
                                    <span>Personal</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <span class="wd-7 ht-7 bg-success rounded-circle"></span>
                                    <span>Payments</span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav flex-column nxl-content-sidebar-item">
                            <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1 d-flex align-items-center justify-content-between">
                                <span>Filter</span>
                                <a href="javascript:void(0);">
                                    <span class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Add New"> <i class="feather-plus"></i> </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <i class="feather-star"></i>
                                    <span>Favorite</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:void(0);">
                                    <i class="feather-bell"></i>
                                    <span>Snoozed</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center justify-content-between" href="javascript:void(0);">
                                    <span class="d-flex align-items-center">
                                        <i class="feather-info me-3"></i>
                                        <span>Important</span>
                                    </span>
                                    <span class="badge bg-soft-success text-success">3</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div> --}}
                @include('seguros.novedades.estadonovedad');
                <!-- [ Content Area ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </main>
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Compose Mail Modal !-->
    <!--! ================================================================ !-->
    <div class="modal fade-scale" id="composeMail" tabindex="-1" aria-labelledby="composeMail" aria-hidden="true"
        data-bs-dismiss="ou">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <!--! BEGIN: [modal-header] !-->
                <div class="modal-header">
                    <h2 class="d-flex flex-column mb-0">
                        <span class="fs-18 fw-bold mb-1">Compose Mail</span>
                        <small class="d-block fs-11 fw-normal text-muted">Compose Your Message</small>
                    </h2>
                    <a href="javascript:void(0)" class="avatar-text avatar-md bg-soft-danger close-icon"
                        data-bs-dismiss="modal">
                        <i class="feather-x text-danger"></i>
                    </a>
                </div>
                <!--! BEGIN: [modal-body] !-->
                <div class="modal-body p-0">
                    <div class="position-relative border-bottom">
                        <div class="px-2 d-flex align-items-center">
                            <div class="p-0 w-100">
                                <input class="form-control border-0 text-dark" name="tomailmodal" placeholder="TO">
                            </div>
                        </div>
                        <a href="javascript:void(0)"
                            class="position-absolute top-50 end-0 translate-middle badge bg-gray-100 border border-gray-3 fs-10 fw-semibold text-uppercase text-dark rounded-pill c-pointer z-index-100"
                            id="ccbccToggleModal"><span data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="CC / BCC" style="font-size: 9px !important">CC / BCC</span></a>
                    </div>
                    <div class="border-bottom mail-cc-bcc-fields" id="ccbccToggleModalFileds" style="display: none">
                        <div class="px-2 w-100 d-flex align-items-center border-bottom">
                            <input class="form-control border-0 text-dark" name="ccmailmodal" placeholder="CC">
                        </div>
                        <div class="px-2 w-100 d-flex align-items-center">
                            <input class="form-control border-0 text-dark" name="bccmailmodal" placeholder="BCC">
                        </div>
                    </div>
                    <div class="px-3 w-100 d-flex align-items-center">
                        <input class="form-control border-0 my-1 w-100 shadow-none" type="email"
                            placeholder="Subject">
                    </div>
                    <div class="editor w-100 m-0">
                        <div class="ht-300 border-bottom-0" id="mailEditorModal"></div>
                    </div>
                </div>
                <!--! BEGIN: [modal-footer] !-->
                <div class="modal-footer d-flex align-items-center justify-content-between">
                    <!--! BEGIN: [mail-editor-action-left] !-->
                    <div class="d-flex align-items-center">
                        <div class="dropdown me-2">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-offset="0, 0">
                                <span class="btn btn-primary dropdown-toggle" data-bs-toggle="tooltip"
                                    data-bs-trigger="hover" title="Send Message"> Send </span>
                            </a>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0)" class="dropdown-item"
                                    data-action-target="#mailActionMessage">
                                    <i class="feather-send me-3"></i>
                                    <span>Instant Send</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item successAlertMessage">
                                    <i class="feather-clock me-3"></i>
                                    <span>Schedule Send</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0)" class="dropdown-item successAlertMessage">
                                    <i class="feather-x me-3"></i>
                                    <span>Discard Now</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item successAlertMessage">
                                    <i class="feather-edit-3 me-3"></i>
                                    <span>Save as Draft</span>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown me-2 d-none d-sm-block">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-offset="0, 0">
                                <span class="btn btn-icon" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    title="Pick Template">
                                    <i class="feather-hash"></i>
                                </span>
                            </a>
                            <div class="dropdown-menu wd-300">
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Welcome you message</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Your issues solved</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Thank you message</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Make a offer message</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Add the Unsubscribe option</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Thank your customer for joining</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-save me-3"></i>
                                    <span>Save as Template</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-sun me-3"></i>
                                    <span>Manage Template</span>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-offset="0, 0">
                                <span class="btn btn-icon" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    title="Upload Attachments">
                                    <i class="feather-upload"></i>
                                </span>
                            </a>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-image me-3"></i>
                                    <span>Upload Images</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-video me-3"></i>
                                    <span>Upload Videos</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-mic me-3"></i>
                                    <span>Upload Musics</span>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="feather-file-text me-3"></i>
                                    <span>Upload Documents</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--! BEGIN: [mail-editor-action-right] !-->
                    <div class="d-flex align-items-center">
                        <div class="dropdown me-2">
                            <a href="javascript:void(0)" data-bs-toggle="dropdown" data-bs-offset="0, 0">
                                <span class="btn btn-icon" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                    title="Editing Actions">
                                    <i class="feather-more-horizontal"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather-type me-3"></i>
                                        <span>Plain Text Mode</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather-check me-3"></i>
                                        <span>Check Spelling</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather-compass me-3"></i>
                                        <span>Smart Compose</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        <i class="feather-feather me-3"></i>
                                        <span>Manage Signature</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="javascript:void(0);" data-bs-dismiss="modal">
                            <span class="btn btn-icon" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="Delete Message">
                                <i class="feather-x"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--! ================================================================ !-->
    <!--! END: Compose Mail Modal !-->
    <!--! ================================================================ !-->


    <!--! ================================================================ !-->
    <!--! [End] Theme Customizer !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/tagify.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/tagify-data.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/quill.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/apps-email-init.min.js') }}"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
</body>

</html>
