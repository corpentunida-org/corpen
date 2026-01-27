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

        .rating-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: auto auto;
            gap: 6px 12px;
            justify-items: center;
            align-items: center;
            margin-top: 15px;
        }

        /* Estrellas */
        .star {
            font-size: 42px;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s, transform 0.15s;
        }

        .star:hover {
            transform: scale(1.15);
        }

        .star.active {
            color: #f1c40f;
        }

        .number {
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }

        .rating-radio {
            display: none;
        }

        .star.active {
            color: #f5c518;
            font-size: 50px;
        }

        .rating-item.active .number {
            color: #d32f2f;
            font-size: 22px;
            font-weight: bold;
        }

        .rating-disabled {
            pointer-events: none;
            opacity: 0.4;
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
                @if (!$quizActivo)
                    <div class="alert alert-dismissible m-4 p-4 d-flex alert-soft-danger-message" role="alert">
                        <div class="me-4 d-none d-md-block"></div>
                        <div>
                            <p class="fw-bold mb-1 text-truncate-1-line">¡Este cuestionario ya no se encuentra activo!
                            </p>
                            <p class="fs-12 fw-medium text-truncate-1-line">El período de disponibilidad ha finalizado.
                                Por favor comuníquese con el área responsable para mayor información.</strong></p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    </div>
                @else
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
                                        <label for="correoUsuario">Correo Corporativo <span
                                                class="req">*</span></label>
                                        <input type="email" id="correoUsuario" name="correoUsuario" required>
                                        <div class="invalid-feedback" style="display:none;">Ingrese su correo
                                            corporativo.
                                        </div>
                                    </div>
                                </section>
                                @foreach ($preguntas as $index => $preg)
                                    <div class="wizard-section">
                                        <h5>{{ $preg['pregunta']->texto }}</h5>
                                        <input type="hidden" name="preguntas[{{ $index }}][idpregunta]"
                                            value="{{ $preg['pregunta']->id }}">
                                        @if ($preg['indicador'])
                                            <div class="answer-content">
                                                <div class="answer-text">
                                                    <p>De click sobre la estrella según corresponda, donde 1 = Muy poco
                                                        satisfecho y 5 = Muy
                                                        satisfecho.</p>
                                                </div>
                                            </div>
                                            <div class="rating-grid" id="rating-grid-{{ $index }}">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <label class="rating-item">
                                                        <input type="radio" class="rating-radio"
                                                            id="rating-radio-{{ $index }}-{{ $i }}"
                                                            name="preguntas[{{ $index }}][idrespuesta]"
                                                            value="{{ $i }}" required>

                                                        <div class="star"
                                                            id="star-{{ $index }}-{{ $i }}">★</div>
                                                        <div class="number"
                                                            id="number-{{ $index }}-{{ $i }}">
                                                            {{ $i }}</div>
                                                    </label>
                                                @endfor
                                            </div>

                                            @if ($preg['pregunta']->id == 2)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input"
                                                        id="checkna-{{ $index }}"
                                                        name="preguntas[{{ $index }}][idrespuesta]"
                                                        value="n/a">
                                                    <label class="custom-control-label c-pointer"
                                                        for="checkna-{{ $index }}">
                                                        No aplica (no he tenido interacción con estas aplicaciones o
                                                        sistemas)
                                                    </label>
                                                </div>
                                            @endif
                                        @else
                                            @foreach ($preg['respuestas'] as $rindex => $resp)
                                                <label class="answer-card">
                                                    <input type="radio"
                                                        name="preguntas[{{ $index }}][idrespuesta]"
                                                        id="preg{{ $index }}_resp{{ $rindex }}"
                                                        value="{{ $resp['id'] }}" required>
                                                    <div class="answer-content">
                                                        <div class="answer-text">
                                                            <p><strong>{{ $rindex + 1 }}.
                                                                </strong>{{ $resp['texto'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endif
                                        <div class="invalid-feedback" style="display:none;">Seleccione una respuesta.
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="wizard-footer">
                                <button type="button" class="wizard-btn prev-btn" disabled>ATRÁS</button>
                                <button type="button" class="wizard-btn next-btn">SIGUIENTE</button>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="card stretch stretch-full short-info-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-4">
                                        <div class="d-flex gap-4 align-items-center">
                                            <div>
                                                <div class="fs-4 fw-bold text-dark"><span class="counter"
                                                        id="timerCounter">00:00:00</span></div>
                                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Tiempo transcurrido
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pt-2">
                                        <div class="progress mt-2 ht-3">
                                            <input type="hidden" name="tiempo_transcurrido"
                                                id="tiempo_transcurrido">
                                            <div class="progress-bar progress-1" role="progressbar"
                                                style="width: 56%;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            console.log("Documento listo");
            document.addEventListener("keydown", function(e) {
                if (
                    e.key === "F5" ||
                    (e.ctrlKey && e.key === "r") ||
                    (e.metaKey && e.key === "r")
                ) {
                    e.preventDefault();
                }
            });

            const counter = document.getElementById('timerCounter');
            const progressBar = document.querySelector('.progress-bar');
            const inputTiempo = document.getElementById('tiempo_transcurrido');
            const MAX_SECONDS = 10 * 60;
            let elapsed = 0;

            function formatTime(seconds) {
                const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
                const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
                const s = String(seconds % 60).padStart(2, '0');
                return `${h}:${m}:${s}`;
            }

            setInterval(() => {
                if (elapsed >= MAX_SECONDS) return;

                elapsed++;
                counter.textContent = formatTime(elapsed);
                if (progressBar) {
                    progressBar.style.width = (elapsed / MAX_SECONDS) * 100 + '%';
                }

                // Guardar valor
                inputTiempo.value = formatTime(elapsed);
            }, 1000);


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
                initRatings($steps[index]);
            }

            function validateInputs(showFeedback = false) {
                let valid = true;
                const $currentSection = $steps.eq(currentStep);

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

                const $noAplica = $currentSection.find('input[type="checkbox"][id^="checkna-"]');

                if ($noAplica.length && $noAplica.is(':checked')) {
                    // ✅ No aplica marcado → no validar radios
                    $currentSection.find('.invalid-feedback').hide();
                } else {
                    const $radios = $currentSection.find('input[type="radio"]:enabled');

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
                }
                return valid;
            }

            function initRatings(section) {
                const items = section.querySelectorAll('.rating-item');

                items.forEach(item => {
                    const radio = item.querySelector('.rating-radio');
                    const star = item.querySelector('.star');

                    // CLICK sobre estrella
                    star.addEventListener('click', () => {
                        if (radio.disabled) return;
                        radio.checked = true;
                        paintStars(section, radio.value);
                    });

                    // Cambio por radio
                    radio.addEventListener('change', () => {
                        paintStars(section, radio.value);
                    });
                });

                // Restaurar estado al volver atrás
                const checked = section.querySelector('.rating-radio:checked');
                paintStars(section, checked ? checked.value : 0);
            }


            function paintStars(section, value) {
                const items = section.querySelectorAll('.rating-item');

                items.forEach(item => {
                    const radio = item.querySelector('.rating-radio');
                    const star = item.querySelector('.star');
                    const number = item.querySelector('.number');

                    if (parseInt(radio.value) <= parseInt(value)) {
                        star.style.color = '#ffc107'; // amarillo
                        number.style.color = '#dc3545'; // rojo
                        star.style.transform = 'scale(1.2)';
                        number.style.transform = 'scale(1.1)';
                    } else {
                        star.style.color = '#ccc';
                        number.style.color = '#999';
                        star.style.transform = 'scale(1)';
                        number.style.transform = 'scale(1)';
                    }
                });
            }
            document.querySelectorAll('[id^="checkna-"]').forEach(checkbox => {

                checkbox.addEventListener('change', function() {

                    const index = this.id.split('-')[1];
                    const grid = document.getElementById('rating-grid-' + index);
                    const section = grid.closest('.wizard-section');
                    const radios = grid.querySelectorAll('input[type="radio"]');

                    if (this.checked) {
                        grid.classList.add('rating-disabled');

                        radios.forEach(radio => {
                            radio.checked = false;
                            radio.required = false;
                            radio.disabled = true;
                        });

                        paintStars(section, 0);

                    } else {
                        grid.classList.remove('rating-disabled');

                        radios.forEach(radio => {
                            radio.disabled = false;
                            radio.required = true;
                        });
                    }
                });
            });



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
                    document.getElementById('tiempo_transcurrido').value = document.getElementById(
                        'timerCounter').textContent;
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
