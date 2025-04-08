<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/dataTables.bs5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />


    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tagify.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/tagify-data.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/quill.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/datepicker.min.css') }}">


    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <!--! END: Custom CSS-->
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
    <script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- ICONOS BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('style')
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <style>
        .uppercase-input {
            text-transform: uppercase;
        }
    </style>
    @auth()
        @if (Auth::user()->actions->count() == 0)
            @include('layouts.norole')
        @else
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
                    <!--! [Start] Header Left !-->
                    <div class="header-left d-flex align-items-center gap-4">
                        <!--! [Start] nxl-head-mobile-toggler !-->
                        <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                            <div class="hamburger hamburger--arrowturn">
                                <div class="hamburger-box">
                                    <div class="hamburger-inner"></div>
                                </div>
                            </div>
                        </a>
                        <!--! [Start] nxl-head-mobile-toggler !-->
                        <!--! [Start] nxl-navigation-toggle !-->
                        <div class="nxl-navigation-toggle">
                            <a href="javascript:void(0);" id="menu-mini-button">
                                <i class="feather-align-left"></i>
                            </a>
                            <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                                <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                        <!--! [End] nxl-navigation-toggle !-->
                        <!--! [Start] nxl-lavel-mega-menu-toggle !-->
                        {{-- Responsive icono de mega menu --}}
                        {{-- <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                        <i class="feather-align-left"></i>
                    </a>
                </div> --}}
                        <!--! [End] nxl-lavel-mega-menu-toggle !-->
                        <!--! [Start] nxl-lavel-mega-menu !-->
                        <div class="nxl-drp-link nxl-lavel-mega-menu">
                            <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                                <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                                    <i class="feather-arrow-left me-2"></i>
                                    <span>Back</span>
                                </a>
                            </div>

                        </div>
                        <!--! [End] nxl-lavel-mega-menu !-->
                    </div>
                    <!--! [End] Header Left !-->

                    <!--! [Start] Header Right !-->
                    <div class="header-right ms-auto">
                        <div class="d-flex align-items-center">

                            <div class="nxl-h-item d-none d-sm-flex">
                                <div class="full-screen-switcher">
                                    <a href="javascript:void(0);" class="nxl-head-link me-0"
                                        onclick="$('body').fullScreenHelper('toggle');">
                                        <i class="feather-maximize maximize"></i>
                                        <i class="feather-minimize minimize"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="nxl-h-item dark-light-theme">
                                <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                                    <i class="feather-moon"></i>
                                </a>
                                <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                                    <i class="feather-sun"></i>
                                </a>
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
                                                <span
                                                    class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item">
                                        <i class="feather-user"></i>
                                        <span>Profile Details</span>
                                    </a>

                                    <div class="dropdown-divider"></div>

                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="feather-log-out"></i>{{ __('Log Out') }}
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--! [End] Header Right !-->
                </div>
            </header>
            <!--! ================================================================ !-->
            <!--! [End] Header !-->
            <!--! ================================================================ !-->
            <!--! ================================================================ !-->
            <!--! [Start] Main Content !-->
            <!--! ================================================================ !-->
            <main class="nxl-container  ">
                <div class="nxl-content">
                    <!-- [ page-header ] start -->
                    <div class="page-header">
                        <div class="page-header-left d-flex align-items-center">
                            @stack('header')
                            <div class="page-header-title">
                                <h5 class="m-b-10">APP Corpentunida</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item">@yield('titlepage', 'Inicio')</li>
                            </ul>
                        </div>
                    </div>
                    <!-- [ page-header ] end -->
                    <!-- [ Main Content ] start -->

                    <div class="main-content">
                        <div class="row">
                            {{ $slot }}
                        </div>
                    </div>


                    <!-- [ Main Content ] end -->
                </div>
                <!-- [ Footer ] start -->
                <footer class="footer">
                    <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                        <span>Corpentunida ©</span>
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                    </p>
                    <div class="d-flex align-items-center gap-4">
                        <a href="https://corpentunida.org.co/" class="fs-11 fw-semibold text-uppercase"
                            target="_blank">web</a>
                    </div>
                </footer>
                <!-- [ Footer ] end -->
            </main>

            <x-modal></x-modal>
            <!--! ================================================================ !-->
            <!--! [End] Main Content !-->
            <!--! ================================================================ !-->
            <!--! ================================================================ !-->

            <!--! Footer Script !-->
            <!--! ================================================================ !-->

            {{-- Bootstrap CDN --}}
            {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> --}}

            <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
            <!-- vendors.min.js {always must need to be top} -->

            <script src="{{ asset('assets/js/customers-init.min.js') }}"></script>
            <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
            <!--! BEGIN: Vendors JS !-->
            <script src="{{ asset('assets/vendors/js/dataTables.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/dataTables.bs5.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>


            <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
            <!--! END: Vendors JS !-->
            <!--! BEGIN: Apps Init  !-->
            <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
            <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>

            <script src="{{ asset('assets/js/projects-init.min.js') }}"></script>
            <!--! END: Apps Init !-->

            <script src="{{ asset('assets/vendors/js/tagify.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/tagify-data.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/quill.min.js') }}"></script>
            <script src="{{ asset('assets/vendors/js/datepicker.min.js') }}"></script>

            <script src="{{ asset('assets/js/proposal-create-init.min.js') }}"></script>
            <!--! END: Apps Init !-->
            @stack('scripts')
        @endif
    @endauth
</body>

</html>
