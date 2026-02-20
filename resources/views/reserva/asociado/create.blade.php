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
                <form id="reservaForm" action="{{ route('reserva.inmueble.store') }}" method="POST">
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
                            <input type="text" class="form-control" id="endDate" name="endDate" required>
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
                        <button type="submit" class="btn btn-success"><i class="bi bi-plus"></i>Crear Reserva</button>
                    </div>
                </form>
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

            /* controla altura total del grid */
            .fc .fc-scrollgrid-sync-table {
                height: auto !important;
            }

            /* contenido interno */
            .fc .fc-daygrid-day-frame {
                min-height: 100px !important;
                padding: 2px !important;
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
        <script src="{{ asset('assets/vendors/js/datepicker.min.js') }}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const $start = $('#fechaInicio');
                const $end = $('#endDate');

                if (!$start.length || !$end.length) return;

                // Instancias datepicker
                const startPicker = new Datepicker($start[0], {
                    format: 'yyyy-mm-dd',
                    autohide: true
                });

                const endPicker = new Datepicker($end[0], {
                    format: 'yyyy-mm-dd',
                    autohide: true
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
                        validRange: function(nowDate) {
                            let start = new Date();
                            start.setHours(0, 0, 0, 0);

                            let end = new Date();
                            end.setDate(end.getDate() + 365);

                            return {
                                start: start,
                                end: end
                            };
                        },
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth'
                        },
                        initialView: 'dayGridMonth',

                        events: [
                            @foreach ($reservas as $reserva)

                                {
                                    title: 'Alistamiento',
                                    start: '{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->subDay()->format('Y-m-d') }}',
                                    end: '{{ $reserva->fecha_inicio }}',
                                    backgroundColor: '#4f545a',
                                    display: 'background'
                                },

                                // RESERVA REAL
                                {
                                    title: 'Reserva',
                                    start: '{{ $reserva->fecha_inicio }}',
                                    end: '{{ \Carbon\Carbon::parse($reserva->fecha_fin)->addDay()->format('Y-m-d') }}',
                                    backgroundColor: 'rgba(0,123,255,0.94)',
                                    borderColor: '#073c8a',
                                    description: 'Reserva activa',
                                    display: 'background'
                                },

                                // BLOQUEO DIA DESPUES
                                {
                                    title: 'Alistamiento',
                                    start: '{{ \Carbon\Carbon::parse($reserva->fecha_fin)->addDay()->format('Y-m-d') }}',
                                    end: '{{ \Carbon\Carbon::parse($reserva->fecha_fin)->addDays(2)->format('Y-m-d') }}',
                                    backgroundColor: '#4f545a',
                                    display: 'background'
                                },
                            @endforeach
                        ],

                        eventClick(info) {
                            info.jsEvent.preventDefault();

                            alert(
                                `Evento: ${info.event.title}\nDescripción: ${info.event.extendedProps.description}`
                            );
                        },

                        dateClick(info) {
                            const events = info.view.calendar.getEvents();

                            const ocupado = events.some(e => {
                                return info.date >= e.start && info.date < e.end;
                            });

                            if (ocupado) return;
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            const clicked = new Date(info.dateStr);
                            clicked.setHours(0, 0, 0, 0);
                            if (clicked < today) {
                                return;
                            }
                            const modal = new bootstrap.Modal(
                                document.getElementById('reservaModal'), {
                                    backdrop: 'static',
                                    keyboard: true
                                }
                            );

                            modal.show();
                            const startDate = new Date(info.dateStr);

                            $('#fechaInicio').val(info.dateStr);
                            $('#endDate').val('');

                            // calcular mínimo permitido = inicio +1 día
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
                const form = document.getElementById('reservaForm');

                if (form) {
                    form.addEventListener('submit', function(event) {
                        const celular = document.getElementById('celular').value;
                        const diffDays = (end - start) / (1000 * 60 * 60 * 24);
                        if (!/^\d{10}$/.test(celular)) {
                            event.preventDefault();
                            alert('El celular debe tener exactamente 10 dígitos.');
                        }
                    });
                }
            });
        </script>
    @endpush
</x-base-layout>
