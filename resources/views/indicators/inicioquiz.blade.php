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
                <div class="card border-top-0 mt-5">
                    <ul class="nav nav-tabs flex-wrap w-100 text-center customers-nav-tabs" id="myTab"
                        role="tablist">
                        <li class="nav-item flex-fill border-top" role="presentation">
                            <a href="" class="nav-link active" data-bs-toggle="tab"
                                data-bs-target="#connectionTab" role="tab" aria-selected="true">QUIZ</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active show">
                            <fieldset>
                                <div class="d-flex align-items-center justify-content-center"
                                    style="height: calc(100vh - 315px)">
                                    <div class="text-center">
                                        <h2 class="fs-16 fw-semibold">Inducción y Reinducción Seguridad Informática</h2>
                                        <p class="fs-12 text-muted">De click en el siguiente botón para iniciar el quiz.
                                        </p>
                                        <a href={{ route('indicators.quiz.preguntas') }}
                                            class="btn bg-soft-primary text-primary" id="btnPresentarQuiz"><i
                                                class="feather-plus me-2"></i>
                                            Presentar Quiz </a>
                                    </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <script>
        document.getElementById('btnPresentarQuiz').addEventListener('click', function(e) {
            let timerInterval;
            Swal.fire({
                title: "¿Deseas iniciar el quiz?",
                html: 'El quiz iniciará automáticamente en <b id="countdown">5</b> segundos.',
                icon: "warning",
                timer: 10000,
                timerProgressBar: true,
                showCancelButton: true,
                confirmButtonText: "Iniciar",
                cancelButtonText: "Cancelar",
                didOpen: () => {
                    const b = document.getElementById('countdown'); // <-- CORRECTO
                    // Inicializar texto antes del intervalo
                    b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    timerInterval = setInterval(() => {
                        let timeLeft = Swal.getTimerLeft();
                        if (timeLeft !== null) {
                            b.textContent = Math.ceil(timeLeft / 1000);
                        }
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('indicators.quiz.preguntas') }}";
                } else if (result.dismiss === Swal.DismissReason.timer) {
                    // Si se acaba el tiempo también inicia el quiz
                    window.location.href = "{{ route('indicators.quiz.preguntas') }}";
                }
            });
        });
    </script>


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
