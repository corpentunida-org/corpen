<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />

    <div class="content-area" data-scrollbar-target="#psScrollbarInit">
        <div class="content-area-header sticky-top"></div>
        <div class="content-area-body p-0">
            <div id="calendar"></div>
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
                    <input type="hidden" name="inmueble_id" id="inmueble_id" value="{{ $inmueble->id }}">
                    <div>
                        <div style="padding: 20px;text-align: justify">
                            <strong>Estimado asociado:</strong>
                            <br><br>Le recordamos tener en cuenta sus planes de viaje, los costos de desplazamiento y cualquier otro gasto en el que pueda incurrir al realizar esta reserva.
                            Es importante que tenga presente que la responsabilidad total de la reserva recae sobre usted.
                            En caso de presentarse inconvenientes,  <strong>Corpentunida no podrá realizar reembolsos del valor consignado</strong>, dado que este corresponde a una donación destinada a la continuidad del beneficio.
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                            <input type="text" class="form-control" id="fechaInicio" name="fechaInicio" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">Fecha de Fin</label>
                            <input type="text" class="form-control" id="endDate" name="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="celular" class="form-label">Número de celular</label>
                            <input type="text" class="form-control" id="celular" name="celular" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Comentario de reserva</label>
                            <textarea class="form-control" id="description" name="description" rows="3" ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Reserva</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            /* Personaliza el fondo del calendario */
            #calendar {
                background-color: white; /* Fondo blanco */
                padding: 10px; /* Opcional: añade margen interno para separarlo */
                border: 1px solid #ddd; /* Opcional: añade borde alrededor del calendario */
                border-radius: 5px; /* Opcional: bordes redondeados */
                position: static; /* Evitar conflictos en el contexto de apilamiento */
                z-index: auto;   /* Dejar que apile naturalmente los elementos */

            }
            .modal-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.5); /* Color de fondo semi-transparente */
                z-index: 1040 !important; /* Nivel de apilamiento detrás del modal */
            }

            .modal {
                z-index: 1055 !important;
            }

            .content-area {
                position: static !important; /* Elimina cualquier posible contexto de apilamiento */
            }

            .nxl-container {
                filter: none !important; /* Elimina cualquier posible filtro */
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{asset('assets/vendors/js/datepicker.min.js')}}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const endDateField = document.getElementById('endDate');

                // Inicializa el Datepicker para endDate
                const endDatePicker = new Datepicker(endDateField, {
                    format: 'yyyy-mm-dd',
                    autohide: true,
                    todayBtn: true,
                    clearBtn: true,
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Selecciona el contenedor del calendario
                var calendarEl = document.getElementById('calendar');

                // Obtiene la fecha actual
                let today = new Date();
                let todayDate = today.toISOString().split('T')[0]; // Obtiene la fecha en formato YYYY-MM-DD

                // Inicializa el calendario
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today', // Botones: anterior, siguiente, hoy
                        center: 'title',        // Título del calendario
                        right: 'dayGridMonth' // Vistas: mes, semana, día
                    },
                    initialView: 'dayGridMonth', // Vista inicial: mes

                    // Lista de eventos
                    events: [
                        @foreach($reservas as $reserva)
                            @if($reserva->nid == '0000000000')
                                {
                                    title: 'Adecuación', // Nombre del evento
                                    start: '{{ $reserva->fecha_inicio }}', // Fecha de inicio obtenida del objeto $reserva
                                    end: '{{ \Carbon\Carbon::parse($reserva->fecha_fin)->addDay()->format('Y-m-d') }}', // Suma 1 día a la fecha de fin
                                    description: 'Alistamiento del apartamento.',
                                    backgroundColor: '#4f545a', // Color del fondo del evento
                                    borderColor: '#1b1c1e'      // Color del borde del evento
                                },
                            @else
                                {
                                    title: 'Reserva', // Nombre del evento
                                    start: '{{ $reserva->fecha_inicio }}', // Fecha de inicio obtenida del objeto $reserva
                                    end: '{{ \Carbon\Carbon::parse($reserva->fecha_fin)->addDay()->format('Y-m-d') }}', // Suma 1 día a la fecha de fin
                                    description: 'Evento fijo para el día actual.',
                                    backgroundColor: 'rgba(0,123,255,0.94)', // Color del fondo del evento
                                    borderColor: '#073c8a'      // Color del borde del evento
                                },
                            @endif
                        @endforeach
                    ],

                    // Evento: Detecta clic en un evento
                    eventClick: function(info) {
                        // Evita el comportamiento predeterminado
                        info.jsEvent.preventDefault();

                        // Muestra una alerta con la información del evento
                        alert(`Hiciste clic en el evento: ${info.event.title}\nDescripción: ${info.event.extendedProps.description}`);
                    },

                    // Evento: Detecta clic en un día
                    dateClick: function(info) {
                        // Abre el modal
                        var modalInstance = new bootstrap.Modal(document.getElementById('reservaModal'), {
                            backdrop: 'static', // Fondo del modal no se cerrará al hacer clic en él
                            keyboard: true      // Habilita cerrar el modal con la tecla "ESC"
                        });
                        modalInstance.show();


                        // Establece la fecha seleccionada en el campo "Fecha de Inicio"
                        document.getElementById('fechaInicio').value = (info.dateStr);


                        // Limpia los campos restantes (opcional)
                        document.getElementById('endDate').value = '';
                        document.getElementById('description').value = '';
                    }
                });

                // Renderiza el calendario en la página
                calendar.render();

                // Obtén los campos de fecha
                const celularField = document.getElementById('celular');
                const startDateField = document.getElementById('fechaInicio');
                const endDateField = document.getElementById('endDate');
                const form = document.getElementById('reservaForm');
                form.addEventListener('submit', function(event) {
                    // Obtén los valores de los campos
                    const startDateValue = startDateField.value;
                    const endDateValue = endDateField.value;

                    // Convierte las fechas a objetos de Date
                    const startDate = new Date(startDateValue);
                    const endDate = new Date(endDateValue);

                    // Verifica si endDate es al menos un día después de startDate
                    const differenceInTime = endDate - startDate; // Diferencia en milisegundos
                    const differenceInDays = differenceInTime / (1000 * 60 * 60 * 24); // Diferencia en días

                    if (differenceInDays < 1) {
                        // Si la diferencia es menor a un día, evita el envío y muestra un mensaje de error
                        event.preventDefault();
                        alert('La fecha de fin debe ser al menos un día posterior a la fecha de inicio.');
                    }

                    const celularValue = celularField.value;
                    const celularRegex = /^\d{10}$/; // Expresión regular para validar 10 dígitos numéricos

                    if (!celularRegex.test(celularValue)) {
                        // Si el celular no cumple con el formato
                        event.preventDefault();
                        alert('El número de celular debe tener exactamente 10 dígitos numéricos.');
                        return;
                    }

                });

            });

        </script>
    @endpush
</x-base-layout>
