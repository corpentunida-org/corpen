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
    <style>
        /* --- CONTENEDOR GENERAL --- */
        .text-uppercase {
            text-transform: uppercase;
        }

        .wizard-card {
            width: 90%;
            max-width: 1100px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        /* --- STEPS SUPERIORES --- */
        .wizard-steps {
            display: flex;
            border-bottom: 1px solid #e5e5e5;
        }

        .wizard-step {
            flex: 1;
            text-align: center;
            padding: 18px 0;
            font-weight: 600;
            color: #555;
            border-bottom: 3px solid transparent;
            user-select: none;
            pointer-events: none;
            /* desactiva click */
        }

        .wizard-step.active {
            color: #3f37c9;
            border-bottom: 3px solid #3f37c9;
        }

        /* --- CUERPO DEL WIZARD --- */
        .wizard-body {
            padding: 50px 70px;
            min-height: 350px;
        }

        .wizard-section {
            display: none;
        }

        .wizard-section.active {
            display: block;
        }

        /* --- FORMULARIO --- */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
        }

        .req {
            color: #d62828;
        }

        /* --- PREGUNTAS --- */
        .question-title h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            font-size: 13px;
            display: none;
        }

        /* --- OPCIONES --- */
        .options-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .option-item {
            width: 100%;
            cursor: pointer;
        }

        .option-item input {
            display: none;
        }

        .option-card {
            border: 1px solid #dcdcdc;
            padding: 18px;
            border-radius: 8px;
            background: #fafafa;
            display: flex;
            gap: 10px;
            align-items: center;
            transition: 0.2s;
        }

        .option-item input:checked+.option-card {
            border-color: #3f37c9;
            background: #eef;
        }

        .option-number {
            font-weight: bold;
            color: #333;
        }

        /* --- FOOTER / BOTONES --- */
        .wizard-footer {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #f0f0f0;
        }

        .wizard-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }

        .prev-btn {
            background: #e7e7f3;
            color: #5a5a7a;
        }

        .prev-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .next-btn {
            background: #4a38f4;
            color: white;
        }

        .answer-card {
            display: block;
            border: 1px dashed #ccc;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: border-color 0.3s, background-color 0.3s;
            background-color: white;
            user-select: none;
        }

        .answer-card:hover {
            border-color: #5850ec;
        }

        .answer-card input[type="radio"] {
            display: none;
        }

        .answer-card.selected {
            border-color: #5850ec;
            background-color: #f0f0ff;
        }

        .answer-content {
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            padding: 8px 12px;
        }

        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5850ec;
            min-width: 24px;
        }

        .answer-text strong {
            font-weight: 600;
            color: #222;
        }

        .answer-text p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-9">
                <form method="POST" action="{{ route('indicators.quiz.store') }}" id="quizForm">
                    @csrf
                    <input type="hidden" id="inputpruebaid" name="pruebaid" value="{{ $pruebaid }}">
                    <div class="card border-top-0 mt-5">
                        <div class="wizard-steps">
                            <div class="wizard-step active">USUARIO</div>
                            @foreach ($preguntas as $index => $preg)
                                <div class="wizard-step">PREGUNTA {{ $index + 1 }}</div>
                            @endforeach
                        </div>
                        <div class="wizard-body">
                            <section class="wizard-section active">
                                <div class="form-group">
                                    <label for="nombreUsuario">Nombre Completo <span class="req">*</span></label>
                                    <input class="form-control text-uppercase" type="text" id="nombreUsuario"
                                        name="nombreUsuario" required>
                                    <div class="invalid-feedback" style="display:none;">Por favor ingrese su nombre
                                        completo.</div>
                                </div>
                                <div class="form-group">
                                    <label for="correoUsuario">Correo Corporativo <span class="req">*</span></label>
                                    <input type="email" id="correoUsuario" name="correoUsuario" required>
                                    <div class="invalid-feedback" style="display:none;">Ingrese su correo corporativo.
                                    </div>
                                </div>
                            </section>

                            @foreach ($preguntas as $index => $preg)
                                <div class="wizard-section">
                                    <h5>{{ $preg['pregunta']->texto }}</h5>
                                    <input type="hidden" name="preguntas[{{ $index }}][idpregunta]"
                                        value="{{ $preg['pregunta']->id }}">
                                    @foreach ($preg['respuestas'] as $rindex => $resp)
                                        <label class="answer-card">
                                            <input type="radio" name="preguntas[{{ $index }}][idrespuesta]"
                                                id="preg{{ $index }}_resp{{ $rindex }}"
                                                value="{{ $resp['id'] }}" required>
                                            <div class="answer-content">
                                                <div class="answer-text">
                                                    <p><strong>{{ $rindex + 1 }}. </strong>{{ $resp['texto'] }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                    <div class="invalid-feedback" style="display:none;">Seleccione una respuesta.</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="wizard-footer">
                            <button type="button" class="wizard-btn prev-btn" disabled>ATRÁS</button>
                            <button type="button" class="wizard-btn next-btn">SIGUIENTE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            document.addEventListener("keydown", function(e) {
                if (
                    e.key === "F5" ||
                    (e.ctrlKey && e.key === "r") ||
                    (e.metaKey && e.key === "r")
                ) {
                    e.preventDefault();
                }
            });

            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.pushState(null, null, location.href);
                alert("No puedes retroceder mientras realizas el cuestionario.");
            };

            document.addEventListener("contextmenu", function(e) {
                e.preventDefault();
            });

            window.onbeforeunload = function() {
                return "El progreso del cuestionario se perderá si sales o recargas la página.";
            };

            const $btnNext = $('.next-btn');
            const $btnPrev = $('.prev-btn');
            let currentStep = 0;
            const $steps = $('.wizard-section');
            const totalSteps = $steps.length;
            let attemptedNext = false;

            function showStep(index) {
                $steps.hide().eq(index).show();
                $btnPrev.prop('disabled', index === 0);
                $btnNext.text(index === totalSteps - 1 ? 'FINALIZAR' : 'SIGUIENTE');
                $('.wizard-step').removeClass('active').eq(index).addClass('active');
            }

            function validateInputs(showFeedback = false) {
                let valid = true;
                const $currentSection = $steps.eq(currentStep);

                // 1️⃣ Validar inputs de texto/email requeridos
                $currentSection.find('input[required]').each(function() {
                    if (!$(this).val()) {
                        valid = false;
                        if (showFeedback) {
                            $(this).addClass('is-invalid');
                            $(this).siblings('.invalid-feedback').show();
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.invalid-feedback').hide();
                    }
                });

                const $radios = $currentSection.find('input[type="radio"]');
                if ($radios.length > 0) {
                    const answered = $radios.is(':checked');
                    if (!answered) {
                        valid = false;
                        if (showFeedback) {
                            $currentSection.find('.invalid-feedback').show();
                        }
                    } else {
                        $currentSection.find('.invalid-feedback').hide();
                    }
                }

                return valid;
            }

            // Mostrar el primer paso
            showStep(currentStep);

            // BOTÓN NEXT
            $btnNext.on('click', function(e) {
                e.preventDefault();
                attemptedNext = true;

                // Validar campos del paso actual
                if (!validateInputs(true)) return;

                // Validación especial del correo (primer paso)
                if (currentStep === 0) {
                    const $correoInput = $('#correoUsuario');                    
                    const correo = $correoInput.val().trim();
                    inputpruebaid = $('#inputpruebaid').val().trim();
                    $.ajax({
                        url: '{{ route('indicators.validar.correo') }}',
                        method: 'POST',
                        data: {
                            correoUsuario: correo,
                            pruebaid: inputpruebaid
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log(response);
                            if (!response.existe) {
                                $correoInput.addClass('is-invalid');
                                $correoInput.siblings('.invalid-feedback')
                                    .text('El correo no está registrado.')
                                    .show();
                                return;
                            }

                            if (response.respondido) {
                                $correoInput.addClass('is-invalid');
                                $correoInput.siblings('.invalid-feedback')
                                    .text('Este usuario ya completó el cuestionario.')
                                    .show();
                                return;
                            }

                            // Todo bien → avanzar
                            $correoInput.removeClass('is-invalid');
                            $correoInput.siblings('.invalid-feedback').hide();

                            currentStep++;
                            attemptedNext = false;
                            showStep(currentStep);
                        },
                        error: function() {        
                            console.log(response);                   
                            $correoInput.addClass('is-invalid');
                            $correoInput.siblings('.invalid-feedback')
                                .text('Error al validar el correo.')
                                .show();
                        }
                    });

                    return; // importante → detener aquí
                }

                // Último paso → enviar formulario
                if (currentStep === totalSteps - 1) {
                    quizProtectionEnabled = false;
                    window.onbeforeunload = null;
                    $('#quizForm').submit();
                    return;
                }

                // Pasos intermedios
                currentStep++;
                attemptedNext = false;
                showStep(currentStep);
            });

            // BOTÓN PREV
            $btnPrev.on('click', function(e) {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            // Selección visual de radios (answer-card)
            document.querySelectorAll('.answer-card input[type="radio"]').forEach(input => {
                input.addEventListener('change', function() {
                    const name = this.name;

                    document.querySelectorAll(`input[name="${name}"]`).forEach(i => {
                        i.closest('.answer-card').classList.remove('selected');
                    });

                    this.closest('.answer-card').classList.add('selected');
                });
            });
        });
    </script>


    <!--! ================================================================ !-->
    <!--! [End] Theme Customizer !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
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
