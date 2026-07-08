<x-base-layout>
    @section('titlepage', 'Crear Quiz')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Crear Quiz</h5>
                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-toggle="tooltip" data-title="Delete"><span class="avatar-text avatar-xs bg-danger"
                                    data-bs-toggle="remove"> </span></div>
                            <div data-toggle="tooltip" data-title="Refresh"><span
                                    class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </span></div>
                            <div data-toggle="tooltip" data-title="Maximize/Minimize"><span
                                    class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </span></div>
                        </div>
                        <div class="filter-dropdown">
                            <div class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                <div data-toggle="tooltip" data-title="Options" class="lh-1"><svg
                                        stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                        stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg></div>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item" href="#"><i><svg
                                            stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="4"></circle>
                                            <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                                        </svg></i>New</a><a class="dropdown-item" href="#"><i><svg
                                            stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect x="3" y="4" width="18" height="18" rx="2"
                                                ry="2">
                                            </rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg></i>Event</a><a class="dropdown-item" href="#"><i><svg
                                            stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg></i>Snoozed</a><a class="dropdown-item" href="#"><i><svg
                                            stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round" height="1em"
                                            width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                        </svg></i>Deleted</a>
                                <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i><svg
                                            stroke="currentColor" fill="none" stroke-width="2"
                                            viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                                            height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path
                                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                            </path>
                                        </svg></i>Settings</a><a class="dropdown-item" href="#"><i><svg
                                            stroke="currentColor" fill="none" stroke-width="2"
                                            viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                                            height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <circle cx="12" cy="12" r="4"></circle>
                                            <line x1="4.93" y1="4.93" x2="9.17" y2="9.17">
                                            </line>
                                            <line x1="14.83" y1="14.83" x2="19.07" y2="19.07">
                                            </line>
                                            <line x1="14.83" y1="9.17" x2="19.07" y2="4.93">
                                            </line>
                                            <line x1="14.83" y1="9.17" x2="18.36" y2="5.64">
                                            </line>
                                            <line x1="4.93" y1="19.07" x2="9.17" y2="14.83">
                                            </line>
                                        </svg></i>Tips &amp; Tricks</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body custom-card-action">
                    <form action="{{ route('indicators.quizes.store') }}" method="POST" id='quizCreateForm'
                        novalidate>
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Título del Quiz</label>
                                <input type="text" class="form-control" name="titulo"
                                    placeholder="Ingrese el título del quiz" required>
                            </div>
                        </div>
                        <div id="preguntas">
                            <div class="card mb-4 pregunta">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">
                                        Pregunta <span class="numero">1</span>
                                    </span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input cambiarTipo" type="checkbox"
                                            id="switchPregunta1">
                                        <label class="form-check-label small" for="switchPregunta1">
                                            Cambiar a escala de valoración
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Pregunta</label>
                                            <input type="text" class="form-control" name="preguntas[0][pregunta]"
                                                placeholder="Escriba la pregunta" required>
                                        </div>
                                    </div>
                                    <div class="contenido-opciones">
                                        <label class="form-label fw-bold mb-1">Respuestas</label>
                                        <div class="text-muted small mb-3">
                                            Digite las respuestas para esta pregunta y marque únicamente la respuesta
                                            correcta.
                                        </div>
                                        <div class="contenedorRespuestas">
                                            <div class="row align-items-center mb-3 respuesta">
                                                <div class="col-md-1 text-center">
                                                    <input class="form-check-input" type="radio"
                                                        name="preguntas[0][correcta]" value="0" checked>
                                                </div>
                                                <div class="col-md-11">
                                                    <input type="text" class="form-control"
                                                        name="preguntas[0][respuestas][]" placeholder="Respuesta 1"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="row align-items-center mb-3 respuesta">
                                                <div class="col-md-1 text-center">
                                                    <input class="form-check-input" type="radio"
                                                        name="preguntas[0][correcta]" value="1">
                                                </div>
                                                <div class="col-md-11">
                                                    <input type="text" class="form-control"
                                                        name="preguntas[0][respuestas][]" placeholder="Respuesta 2"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="row align-items-center mb-3 respuesta">
                                                <div class="col-md-1 text-center">
                                                    <input class="form-check-input" type="radio"
                                                        name="preguntas[0][correcta]" value="2">
                                                </div>
                                                <div class="col-md-11">
                                                    <input type="text" class="form-control"
                                                        name="preguntas[0][respuestas][]" placeholder="Respuesta 3"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="row align-items-center mb-3 respuesta">
                                                <div class="col-md-1 text-center">
                                                    <input class="form-check-input" type="radio"
                                                        name="preguntas[0][correcta]" value="3">
                                                </div>
                                                <div class="col-md-11">
                                                    <input type="text" class="form-control"
                                                        name="preguntas[0][respuestas][]" placeholder="Respuesta 4"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button"
                                        class="btn btn-outline-primary btn-sm agregarRespuesta mt-2">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Agregar respuesta
                                    </button>
                                    </div>
                                    <div class="contenido-estrellas d-none">
                                        <div class="alert alert-info">
                                            Esta pregunta permite al usuario asignar una calificación utilizando una
                                            escala de 1 a 5 estrellas.
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" id="agregarPregunta">
                            Agregar otra pregunta
                        </button>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success mt-4"><i class="bi bi-plus"></i> Crear
                                Quiz</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let contador = 1;
        document.getElementById('agregarPregunta').addEventListener('click', function() {
            const original = document.querySelector('.pregunta');
            const copia = original.cloneNode(true);
            const header = copia.querySelector('.card-header');

            header.classList.remove('fw-bold');
            header.classList.add('d-flex', 'justify-content-between', 'align-items-center');

            header.innerHTML = `
                <span class="fw-bold">
                    Pregunta <span class="numero">${contador + 1}</span>
                </span>

                <button type="button"
                        class="btn btn-danger btn-sm rounded-circle eliminarPregunta"
                        title="Eliminar pregunta"
                        style="width:32px;height:32px;">
                    <i class="bi bi-x"></i>
                </button>
            `;
            copia.querySelector('.numero').innerText = contador + 1;
            copia.querySelectorAll('input').forEach(input => {
                if (input.type == 'text') {
                    input.value = '';
                    input.required = true;
                }
                if (input.type == 'radio') {
                    input.checked = false;
                }
                let name = input.getAttribute('name');
                name = name.replace(/\[\d+\]/, '[' + contador + ']');
                input.setAttribute('name', name);
            });
            const primerRadio = copia.querySelector('input[type="radio"]');
            if (primerRadio) {
                primerRadio.checked = true;
            }
            document.getElementById('preguntas').appendChild(copia);
            contador++;
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.eliminarPregunta');
            if (!btn) return;

            btn.closest('.pregunta').remove();

            document.querySelectorAll('.pregunta').forEach(function(card, index) {

                card.querySelector('.numero').innerText = index + 1;

                card.querySelectorAll('input').forEach(function(input) {

                    let name = input.getAttribute('name');

                    if (name) {
                        input.setAttribute(
                            'name',
                            name.replace(/\[\d+\]/, '[' + index + ']')
                        );
                    }

                });

            });

            contador = document.querySelectorAll('.pregunta').length;

        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.agregarRespuesta')) {
                const boton = e.target.closest('.agregarRespuesta');
                const pregunta = boton.closest('.pregunta');
                const contenedor = pregunta.querySelector('.contenedorRespuestas');
                const indicePregunta = [...document.querySelectorAll('.pregunta')].indexOf(pregunta);
                const cantidad = contenedor.querySelectorAll('.respuesta').length;

                const nueva = document.createElement('div');

                nueva.className = 'row align-items-center mb-3 respuesta';

                nueva.innerHTML = `
                    <div class="col-md-1 text-center">
                        <input class="form-check-input"
                            type="radio"
                            name="preguntas[${indicePregunta}][correcta]"
                            value="${cantidad}">
                    </div>

                    <div class="col-md-10">
                        <input type="text"
                            class="form-control"
                            name="preguntas[${indicePregunta}][respuestas][]"
                            placeholder="Respuesta ${cantidad + 1}"
                            required>
                    </div>

                    <div class="col-md-1 text-center">
                        <button type="button"
                                class="btn btn-danger btn-sm avatar-text avatar-md eliminarRespuesta"
                                title="Eliminar respuesta">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
                contenedor.appendChild(nueva);
            }
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.eliminarRespuesta');
            if (btn) {
                btn.closest('.respuesta').remove();
            }
        });

        $('#quizCreateForm').submit(function(event) {
            var form = this;
            if (!form.checkValidity()) {
                $(form).addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            }
        });

        document.addEventListener('change', function(e) {

            if (!e.target.classList.contains('cambiarTipo')) return;

            const card = e.target.closest('.pregunta');

            const opciones = card.querySelector('.contenido-opciones');
            const abierta = card.querySelector('.contenido-estrellas');

            if (e.target.checked) {
                opciones.classList.add('d-none');
                abierta.classList.remove('d-none');
            } else {
                opciones.classList.remove('d-none');
                abierta.classList.add('d-none');
            }

        });
    </script>
</x-base-layout>
