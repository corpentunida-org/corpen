<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />
    <x-error />

    <div class="card" data-scrollbar-target="#psScrollbarInit">
        <div class="card-body p-4">
            <div id="calendar" class="p-4"></div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="reservaModal" tabindex="-1" aria-labelledby="reservaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservaModalLabel">Crear Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="reservaForm" action="{{ route('reserva.inmueble.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="inmueble_id" id="inmueble_id" value="{{ $inmueble->id }}">
                        <div>
                            <p><span class="fw-semibold mt-4">Estimado asociado:</span>
                            <p>Le recordamos tener en cuenta sus planes de viaje, los costos de desplazamiento y
                                cualquier otro gasto en el que pueda incurrir al realizar esta reserva.
                                Es importante que tenga presente que la responsabilidad total de la reserva recae sobre
                                usted.
                                En caso de presentarse inconvenientes,<span class="fw-semibold mt-4"> Corpentunida no
                                    podrá realizar reembolsos del valor consignado</span>, dado que este corresponde a
                                una donación destinada a la continuidad del beneficio.</p>
                        </div>
                        </p>
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                            <input type="text" class="form-control" id="fechaInicio" name="fechaInicio" required
                                readonly>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">Fecha de Fin</label>
                            <input type="text" class="form-control" id="endDate" name="endDate"
                                placeholder="Seleccione una fecha" required>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="celular" class="form-label">Número de celular principal<span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="celular" name="celular" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Número de celular adicional</label>
                                <input type="number" class="form-control" id="celulartwo" name="celulartwo" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Comentario de reserva</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="btnConfirmar"><i class="bi bi-plus"></i>
                            Crear Reserva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog  modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">
                        Lineamientos para el uso de los apartamentos

                        <span class="d-block fs-12 fw-normal text-muted" style="line-height:1.2; margin-top:2px;">
                            Con el fin de garantizar un uso adecuado, equitativo y organizado de estos espacios,
                            se establecen las siguientes condiciones:
                        </span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Reservas</h6>
                    <ul>
                        <li>Este es el <strong>único canal</strong> para realizar una reserva.</li>
                        <li>El asociado debe <strong>estar presente durante la estadía</strong>.</li>
                        <li>
                            <span class="text-danger">
                                ❌ No se permite reservar para familiares, amigos o terceros.
                            </span>
                        </li>
                        <li>⏳ <strong>Estadía máxima:</strong> 5 días / 4 noches.</li>
                        <li><strong>Capacidad máxima:</strong> 6 personas.</li>
                        <li><span class="text-danger">No se permite el ingreso de mascotas.</span></li>
                        <li>El asociado es responsable del cuidado de <strong>menores y adultos mayores</strong>.</li>
                    </ul>

                    <h6 class="mt-4">Proceso de pago</h6>

                    <p class="m-0">
                        Una vez realizada la <strong>pre-reserva</strong>, el sistema bloqueará automáticamente
                        los días seleccionados.
                    </p>

                    <p class="m-0">
                        El asociado tendrá <span class="text-danger"><strong>3 días</strong></span> para realizar el
                        pago
                        y cargar el comprobante.
                        Si no se registra el pago dentro de este plazo, las fechas quedarán disponibles para otro
                        asociado.
                    </p>
                    <p>
                        <strong class="text-bold">¿Cómo pagar?</strong>
                        <a href="https://corpentunida.org.co/reservas/#contOpcionesPago" class="text-primary fw-bold">Clic aquí para conocer cómo realizar el pago</a>
                    </p>
                    <p>
                        <span class="text-danger fw-bold">Importante:</span><br>
                        El aporte <strong>no corresponde a un alquiler</strong>, ya que el uso del apartamento es
                        gratuito.
                        Este valor cubre únicamente gastos de <strong>aseo y administración</strong>.
                    </p>

                    <h6 class="mt-4">Cancelaciones y cambios</h6>
                    <ul>
                        <li><span class="text-danger">No se realizarán devoluciones de dinero.</span></li>
                        <li>Las reprogramaciones solo se evaluarán en casos de <strong>fuerza mayor</strong>.</li>
                    </ul>

                    <h6 class="mt-4">Entrega y cuidado del inmueble</h6>
                    <p>
                        El apartamento se entrega en óptimas condiciones y debe devolverse en el mismo estado.
                        Cualquier daño será <strong>responsabilidad del asociado</strong>.
                    </p>

                    <h6 class="mt-4">Logística de ingreso</h6>
                    <ul>
                        <li>
                            En cada ciudad (<strong>Santa Marta y Armenia</strong>) hay un encargado
                            de la entrega de llaves. Se contactará previamente al asociado.
                        </li>
                        <li>Se debe firmar un <strong>acta al momento de ingreso y salida</strong> del inmueble.</li>
                        <li>
                            <span class="text-danger">
                                Nota: El valor aportado no incluye costos adicionales de administración
                                o uso de zonas comunes.
                            </span>
                        </li>
                        <li class="text-muted">
                            Deseamos que estos espacios sean de bendición, descanso y renovación
                            para cada familia pastoral.
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        id="cerrarTodo">Cancelar</button>

                    <button type="button" class="btn btn-success" id="confirmSubmit">
                        Confirmar crear reserva
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('style')
        <style>
            /* Personaliza el fondo del calendario */
            #calendar {
                background-color: white;
                padding: 10px;
                position: static;
                /*z-index: auto;*/
            }

            /* color del dia actual */
            .fc .fc-daygrid-day.fc-day-today {
                background-color: #fff9e2 !important;
            }

            /* por si el tema usa capa interna */
            .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-frame {
                background-color: #fff9e2 !important;
            }

            /* controla altura total del grid */
            .fc .fc-scrollgrid-sync-table {
                height: auto !important;
            }

            /* contenido interno */
            .fc .fc-daygrid-day-frame {
                min-height: 100px !important;
                padding: 2px !important;
            }

            /* color texto eventos allday */
            .fc-event-title {
                color: #2b1a55 !important;
            }

            .modal-backdrop {
                position: fixed;
                width: 100vw;
                height: 100vh;
                z-index: 1040 !important;
            }

            .modal {
                z-index: 1055 !important;
            }

            .nxl-container {
                filter: none !important;

            }

            @media (max-width: 992px) {
                .fc .fc-daygrid-day-frame {
                    min-height: 70px;
                    font-size: 12px;
                }
            }

            @media (max-width: 576px) {
                #calendar {
                    padding: 0 !important;
                }

                .card-body {
                    padding: 0 !important;
                }

                .fc-header-toolbar {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 6px;
                }

                .fc-toolbar-chunk:nth-child(2) {
                    order: 1;
                    width: 100%;
                    display: flex;
                    justify-content: center;
                }

                .fc-toolbar-chunk:nth-child(1),
                .fc-toolbar-chunk:nth-child(3) {
                    order: 2;
                    display: flex;
                    justify-content: center;
                    gap: 6px;
                }

                .fc-dayGridMonth-button {
                    display: none !important;
                }

                .fc-toolbar-title {
                    padding-top: 10px;
                }

                .fc .fc-button {
                    padding: 2px 6px !important;
                    font-size: 12px !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const $start = $('#fechaInicio');
                const $end = $('#endDate');
                const today = new Date();

                if (!$start.length || !$end.length) return;

                /* =========================
                   BLOQUEOS INPUT
                ========================= */
                $start.on('keydown paste', function(e) {
                    e.preventDefault();
                });

                $end.on('keydown paste', function(e) {
                    e.preventDefault();
                });

                $start.on('change', function() {
                    $(this).val($(this).data('locked') || '');
                });

                const startPicker = new Datepicker($start[0], {
                    format: 'yyyy-mm-dd',
                    autohide: true,
                    minDate: today
                });
                $start.prop('readonly', true);
                $start.css('pointer-events', 'none');
                const endPicker = new Datepicker($end[0], {
                    format: 'yyyy-mm-dd',
                    autohide: true,
                    minDate: today
                });

                /* =========================
                   FULLCALENDAR INIT
                ========================= */
                const calendarEl = document.getElementById('calendar');

                if (calendarEl) {
                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        height: "auto",
                        locale: 'es',
                        contentHeight: "auto",
                        expandRows: false,

                        validRange: function() {
                            let start = new Date();
                            start.setHours(0, 0, 0, 0);

                            let end = new Date();
                            end.setDate(end.getDate() + 365);

                            return {
                                start,
                                end
                            };
                        },

                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth'
                        },

                        initialView: 'dayGridMonth',

                        events: [
                            @foreach ($reservas as $r)
                                {
                                    title: 'Reservado',
                                    start: '{{ $r->fecha_inicio }}',
                                    end: '{{ \Carbon\Carbon::parse($r->fecha_fin)->addDay()->format('Y-m-d') }}',
                                    color: '#c8b6ff',
                                    textColor: '#2b1a55',
                                    extendedProps: {
                                        tipo: 'reserva'
                                    }
                                }, {
                                    title: 'Alistamiento',
                                    start: '{{ \Carbon\Carbon::parse($r->fecha_inicio)->subDay()->format('Y-m-d') }}',
                                    end: '{{ $r->fecha_inicio }}',
                                    color: '#E4E7EB',
                                    textColor: '#000',
                                    extendedProps: {
                                        tipo: 'alistamiento'
                                    }
                                }, {
                                    title: 'Alistamiento',
                                    start: '{{ \Carbon\Carbon::parse($r->fecha_fin)->addDay()->format('Y-m-d') }}',
                                    end: '{{ \Carbon\Carbon::parse($r->fecha_fin)->addDays(2)->format('Y-m-d') }}',
                                    color: '#E4E7EB',
                                    textColor: '#000',
                                    extendedProps: {
                                        tipo: 'alistamiento'
                                    }
                                },
                            @endforeach
                        ],

                        eventContent: function(arg) {
                            let icon = arg.event.extendedProps.tipo === 'reserva' ?
                                'bi-calendar-check' :
                                'bi-clock-history';

                            return {
                                html: `<i class="bi ${icon} me-1 ms-2"></i> ${arg.event.title}`
                            };
                        },

                        eventClick(info) {
                            let tipo = info.event.extendedProps.tipo;

                            let icono = tipo === "reserva" ? "error" : "warning";
                            let color = tipo === "reserva" ? "#6f42c1" : "#6c757d";
                            let titulo = tipo === "reserva" ?
                                "Ya se encuentra reservado" :
                                "Inmueble en " + info.event.title;

                            Swal.fire({
                                title: titulo,
                                text: "Fecha: " + info.event.startStr,
                                icon: icono,
                                confirmButtonColor: color,
                                confirmButtonText: "Cerrar"
                            });
                        },

                        dateClick(info) {

                            const events = info.view.calendar.getEvents();

                            const ocupado = events.some(e =>
                                info.date >= e.start && info.date < e.end
                            );

                            if (ocupado) return;

                            const today = new Date();
                            today.setHours(0, 0, 0, 0);

                            const clicked = new Date(info.dateStr);
                            clicked.setHours(0, 0, 0, 0);

                            if (clicked < today) return;

                            const modal = new bootstrap.Modal(
                                document.getElementById('reservaModal'), {
                                    backdrop: 'static',
                                    keyboard: true
                                }
                            );

                            modal.show();

                            const startDate = new Date(info.dateStr);

                            // 🔥 fecha inicio bloqueada correctamente
                            $('#fechaInicio')
                                .val(info.dateStr)
                                .data('locked', info.dateStr);

                            $('#endDate').val('');

                            // 🔥 fin mínimo = inicio + 1 día
                            const minEnd = new Date(startDate);
                            minEnd.setDate(minEnd.getDate() + 1);

                            endPicker.setOptions({
                                minDate: minEnd
                            });
                        },

                        dayCellDidMount(arg) {
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);

                            const cellDate = new Date(arg.date);
                            cellDate.setHours(0, 0, 0, 0);

                            if (cellDate < today) {
                                arg.el.style.backgroundColor = "#f1f1f1";
                                arg.el.style.opacity = "0.6";
                                arg.el.style.pointerEvents = "none";
                            }
                        }

                    });

                    calendar.render();
                }

                /* =========================
                   VALIDACIÓN FORM
                ========================= */

                document.getElementById('btnConfirmar').addEventListener('click', function() {

                    const form = document.getElementById('reservaForm');
                    if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        return;
                    }
                    const celular = document.getElementById('celular').value;

                    if (!/^\d{10}$/.test(celular)) {
                        alert('El celular debe tener exactamente 10 dígitos.');
                        return;
                    }

                    const modal1El = document.getElementById('reservaModal');
                    const modal1 = bootstrap.Modal.getInstance(modal1El);
                    modal1.hide();

                    // 🟢 ABRIR MODAL 2
                    const modal2 = new bootstrap.Modal(document.getElementById('confirmModal'));
                    modal2.show();
                });

                $('#cerrarTodo').on('click', function() {

                    $('.modal.show').each(function() {
                        $(this).modal('hide');
                    });

                    const form = $('#reservaForm');
                    if (form.length) {
                        form[0].reset();
                        form.removeClass('was-validated');
                    }
                });
                $('#confirmSubmit').on('click', function() {
                    const btn = $('#confirmSubmit');
                    btn.prop('disabled', true);
                    btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Procesando...');
                    const form = $('#reservaForm');
                    if (form.length) {
                        form.submit();
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>
