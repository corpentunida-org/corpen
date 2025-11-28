<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Corpentunida') }}</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/select2-theme.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/jquery.steps.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/quill.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/datepicker.min.css') }}">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}">

    <!-- ICONOS BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!--  -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-9">
                <div class="card mt-5 p-3">
                    <div class="card-body p-4">
                        <div class="card-body task-header">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="wd-50 ht-50 bg-soft-primary text-primary lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date">
                                        <span class="fs-18 fw-bold mb-1 d-block">{{ $puntaje }}</span>
                                    </div>
                                    <div class="text-dark">
                                        <a href="javascript:void(0);"
                                            class="fw-bold text-truncate-1-line">{{ $nombre }}</a>
                                        <span
                                            class="fs-11 fw-normal text-muted text-truncate-1-line">{{ $fecha }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @foreach ($resultado as $item)
                            <div class="card p-3 m-3 {{ $item['acertada'] ? 'border-success' : 'border-danger' }}">
                                <h5><strong>Pregunta:</strong> {{ $item['pregunta'] }}</h5>
                                <div class="ps-3">
                                    <a>Tu respuesta:</a>
                                    <span class="fs-12 {{ $item['acertada'] ? 'text-success' : 'text-danger' }}">{{ $item['respuesta_usuario'] }}</span>
                                </div>
                                {{-- <p><strong>Tu respuesta:</strong>
                                    <span
                                        class="{{ $item['acertada'] ? 'text-success' : 'text-danger' }}">{{ $item['respuesta_usuario'] }}</span>
                                </p> --}}
                                @unless ($item['acertada'])
                                    {{-- <p><strong>Correcta:</strong>
                                        <span class="text-success">
                                            {{ $item['respuesta_correcta'] }}
                                        </span>
                                    </p> --}}
                                    <div class="ps-3">
                                    <a>Correcta:</a>
                                    <span class="fs-12 text-success">{{ $item['respuesta_usuario'] }}</span>
                                </div>
                                @endunless
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <script></script>


    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('assets/vendors/js/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/select2-active.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/quill.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/datepicker.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/projects-create-init.min.js') }}"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
</body>

</html>
