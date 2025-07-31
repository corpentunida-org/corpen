<x-base-layout>
    <!-- Bootstrap CSS -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> ERROR--}}

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        /* Estilos generales para inputs */
        .form-control,
        .form-select {
            background-color: #e0f7f5;
            border: 1px solid #80cbc4;
            border-radius: 10px;
            padding: 10px 14px;
            color: #004d40;
            transition: all 0.3s ease-in-out;
        }

        .form-control:hover,
        .form-select:hover {
            background-color: #d4f5f3;
            cursor: pointer;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff;
            border-color: #26a69a;
            box-shadow: 0 0 8px rgba(38, 166, 154, 0.3);
        }

        /* Etiquetas de los campos */
        .form-label {
            font-weight: 600;
            color: #00695c;
        }

        /* Estilo para campos específicos con .form-field */
        .form-field {
            background-color: #f8f9fc !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }

        .form-field:focus {
            border-color: #8A2BE2 !important;
            box-shadow: 0 0 0 0.2rem rgba(138, 43, 226, 0.25);
        }

        /* Botón personalizado */
        .btn-custom {
            background-color: #26a69a;
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-custom:hover {
            background-color: #00796b;
        }

        /* Grupo de entrada con íconos */
        .input-group-text {
            background-color: #ffffff;
            border: none;
            color: #00796b;
        }

        /* Estilos para acordeón */
        .accordion-button {
            background-color: #b2dfdb;
            color: #004d40;
            font-weight: bold;
        }

        .accordion-button:not(.collapsed) {
            background-color: #80cbc4;
            color: #00332e;
        }

        .accordion-body {
            background-color: #e6f5f3;
            border-left: 4px solid #26a69a;
            border-radius: 0 0 10px 10px;
            padding: 20px;
        }
    </style>



    <div class="container mt-5">
       <div class="card shadow-lg rounded custom-card">
            <div class="card-header custom-header">
                <h4 class="mb-0">Formulario de Solicitud de Crédito</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('creditos.estado1.calcular') }}" method="POST">
                    @csrf

                    {{-- Acordeón --}}
                    <div class="accordion mb-4" id="creditAccordion">
   
                        <!-- Sección 1: Tipo de Crédito -->
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed custom-accordion-btn" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    <i class="bi bi-cash-coin me-2 text-success"></i> Sección 1: Tipo de Crédito
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#creditAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label for="tipo_credito" class="form-label">
                                            <i class="bi bi-bank2 me-1 text-primary"></i> Seleccione el tipo de crédito:
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-ui-checks"></i></span>
                                            <select class="form-select" name="tipo_credito" id="tipo_credito" required>
                                                <option value="">-- Seleccione --</option>
                                                <option value="1">RapiCréditos</option>
                                                <option value="2">Crédito Congregación</option>
                                                <option value="3">Libre Inversión</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Sección 2 -->
                        <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="bi bi-ui-checks-grid me-2"></i> Sección 2: Detalle según tipo de crédito
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#creditAccordion">
                            <div class="accordion-body">
                            <div id="rapicredito_options" style="display: none;">
                                <label for="tipo_rapicredito" class="form-label">
                                <i class="bi bi-card-list me-1"></i> Seleccione el tipo de RapiCrédito:
                                </label>
                                <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-checklist"></i></span>
                                <select class="form-select" name="tipo_rapicredito" id="tipo_rapicredito">
                                    <option value="">-- Seleccione --</option>
                                    <option value="educativo">Educativo</option>
                                    <option value="salud">Credi - Salud</option>
                                    <option value="libre">Rapi Crédito Libre Inversión</option>
                                    <option value="seguro">Seguro Todo Riesgo Vehicular</option>
                                </select>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>

                        <!-- Sección 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    Sección 3: Información Adicional (Solo RapiCréditos)
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#creditAccordion">
                                <div class="accordion-body">
                                    <div id="rapicredito_extra" style="display: none;">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="valor_solicitado" class="form-label"><i class="fas fa-dollar-sign me-1"></i>Valor Solicitado</label>
                                                <input type="number" name="valor_solicitado" id="valor_solicitado" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="plazo_solicitado" class="form-label"><i class="fas fa-calendar-alt me-1"></i>Plazo Solicitado (meses)</label>
                                                <input type="number" name="plazo_solicitado" id="plazo_solicitado" class="form-control">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="tipo_cuota" class="form-label"><i class="fas fa-list-ol me-1"></i>Tipo de Cuota</label>
                                            <select class="form-select" name="tipo_cuota" id="tipo_cuota">
                                                <option value="">-- Seleccione --</option>
                                                <option value="fija">Fija</option>
                                                <option value="variable">Variable</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label"><i class="fas fa-university me-1"></i>¿Actualmente tiene crédito en Corpentunida?</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tiene_credito" id="credito_si" value="si">
                                                <label class="form-check-label" for="credito_si">Sí</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tiene_credito" id="credito_no" value="no">
                                                <label class="form-check-label" for="credito_no">No</label>
                                            </div>
                                        </div>

                                        <div class="mb-3" id="cual_credito_container" style="display: none;">
                                            <label for="cual_credito" class="form-label"><i class="fas fa-question-circle me-1"></i>¿Cuál crédito tiene?</label>
                                            <input type="text" name="cual_credito" id="cual_credito" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label for="necesidad_credito" class="form-label"><i class="fas fa-comment-dollar me-1"></i>¿Para qué necesita el crédito?</label>
                                            <textarea name="necesidad_credito" id="necesidad_credito" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                    Sección 4: Información del Pastor (Solo si seleccionó RapiCréditos)
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#creditAccordion">
                                <div class="accordion-body">
                                    <div id="rapicredito_pastor" style="display: none;">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_nombre" class="form-label"><i class="fas fa-user me-1"></i>Nombre completo del pastor</label>
                                                <input type="text" name="pastor_nombre" id="pastor_nombre" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pastor_cedula" class="form-label"><i class="fas fa-id-card me-1"></i>No. Cédula</label>
                                                <input type="text" name="pastor_cedula" id="pastor_cedula" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="pastor_nacimiento" class="form-label"><i class="fas fa-birthday-cake me-1"></i>Fecha de Nacimiento</label>
                                                <input type="date" name="pastor_nacimiento" id="pastor_nacimiento" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pastor_edad" class="form-label"><i class="fas fa-hourglass-half me-1"></i>Edad</label>
                                                <input type="number" name="pastor_edad" id="pastor_edad" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pastor_celular" class="form-label"><i class="fas fa-mobile-alt me-1"></i>Celular</label>
                                                <input type="text" name="pastor_celular" id="pastor_celular" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_telefono" class="form-label"><i class="fas fa-phone me-1"></i>Teléfono</label>
                                                <input type="text" name="pastor_telefono" id="pastor_telefono" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pastor_whatsapp" class="form-label"><i class="fab fa-whatsapp me-1"></i>Línea de WhatsApp</label>
                                                <input type="text" name="pastor_whatsapp" id="pastor_whatsapp" class="form-control">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="pastor_direccion" class="form-label"><i class="fas fa-map-marker-alt me-1"></i>Dirección de domicilio</label>
                                            <input type="text" name="pastor_direccion" id="pastor_direccion" class="form-control">
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_municipio" class="form-label"><i class="fas fa-city me-1"></i>Municipio y Departamento</label>
                                                <input type="text" name="pastor_municipio" id="pastor_municipio" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pastor_congregacion" class="form-label"><i class="fas fa-church me-1"></i>Congregación que administra</label>
                                                <input type="text" name="pastor_congregacion" id="pastor_congregacion" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_correo" class="form-label"><i class="fas fa-envelope me-1"></i>Correo electrónico</label>
                                                <input type="email" name="pastor_correo" id="pastor_correo" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pastor_fecha_corpentunida" class="form-label"><i class="fas fa-calendar-check me-1"></i>Fecha de ingreso a Corpentunida</label>
                                                <input type="date" name="pastor_fecha_corpentunida" id="pastor_fecha_corpentunida" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_fecha_ministerio" class="form-label"><i class="fas fa-calendar me-1"></i>Fecha de ingreso al ministerio</label>
                                                <input type="date" name="pastor_fecha_ministerio" id="pastor_fecha_ministerio" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pastor_distrito" class="form-label"><i class="fas fa-map me-1"></i>Distrito</label>
                                                <input type="text" name="pastor_distrito" id="pastor_distrito" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="pastor_peso" class="form-label"><i class="fas fa-weight me-1"></i>Peso</label>
                                                <input type="text" name="pastor_peso" id="pastor_peso" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pastor_altura" class="form-label"><i class="fas fa-ruler-vertical me-1"></i>Altura</label>
                                                <input type="text" name="pastor_altura" id="pastor_altura" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="pastor_eps" class="form-label"><i class="fas fa-hospital me-1"></i>EPS</label>
                                                <input type="text" name="pastor_eps" id="pastor_eps" class="form-control">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="pastor_enfermedades" class="form-label"><i class="fas fa-notes-medical me-1"></i>Detalle de enfermedades</label>
                                            <textarea name="pastor_enfermedades" id="pastor_enfermedades" class="form-control" rows="2"></textarea>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_conyugue" class="form-label"><i class="fas fa-user-friends me-1"></i>Nombre de su cónyuge</label>
                                                <input type="text" name="pastor_conyugue" id="pastor_conyugue" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pastor_cc_conyugue" class="form-label"><i class="fas fa-id-card-alt me-1"></i>C.C. de su esposa</label>
                                                <input type="text" name="pastor_cc_conyugue" id="pastor_cc_conyugue" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="pastor_celular_esposa" class="form-label"><i class="fas fa-phone-alt me-1"></i>Celular de su esposa</label>
                                                <input type="text" name="pastor_celular_esposa" id="pastor_celular_esposa" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="pastor_hijos" class="form-label"><i class="fas fa-baby me-1"></i>No. de hijos</label>
                                                <input type="number" name="pastor_hijos" id="pastor_hijos" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="pastor_personas_cargo" class="form-label"><i class="fas fa-users me-1"></i>Personas a cargo</label>
                                                <input type="number" name="pastor_personas_cargo" id="pastor_personas_cargo" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Ver Tabla de Amortizacion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JavaScript para lógica condicional y tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elementos
            const tipoCredito = document.getElementById('tipo_credito');
            const rapicreditoOptions = document.getElementById('rapicredito_options');
            const rapicreditoExtra = document.getElementById('rapicredito_extra');
            const rapicreditoPastor = document.getElementById('rapicredito_pastor');
            const cualCreditoContainer = document.getElementById('cual_credito_container');

            // Mostrar campos relacionados si se elige RapiCréditos
            tipoCredito.addEventListener('change', function () {
                const isRapicredito = this.value === '1';

                if (rapicreditoOptions) rapicreditoOptions.style.display = isRapicredito ? 'block' : 'none';
                if (rapicreditoExtra) rapicreditoExtra.style.display = isRapicredito ? 'block' : 'none';
                if (rapicreditoPastor) rapicreditoPastor.style.display = isRapicredito ? 'block' : 'none';
            });

            // Mostrar campo adicional si se elige "Sí" en "¿Tiene otro crédito?"
            const radiosCredito = document.querySelectorAll('input[name="tiene_credito"]');
            radiosCredito.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (cualCreditoContainer) {
                        cualCreditoContainer.style.display = (this.value === 'si') ? 'block' : 'none';
                    }
                });
            });

            // Activar tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltips].forEach(el => new bootstrap.Tooltip(el));
        });

        // Validación de formularios Bootstrap
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</x-base-layout>
