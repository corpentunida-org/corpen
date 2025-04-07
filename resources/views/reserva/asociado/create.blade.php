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
                <form id="reservaForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="startDate" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="endDate" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
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
            .modal {
                z-index: 1055 !important;
            }

            .modal-backdrop {
                z-index: 1045 !important;
            }
            .content-area {
                position: static !important; /* Elimina cualquier posible contexto de apilamiento */
                z-index: auto !important; /* Garantiza que no interfiera */

            }



        </style>
    @endpush

    @push('scripts')
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
                        {
                            title: 'Evento para hoy', // Nombre del evento
                            start: todayDate,       // Fecha de inicio: hoy
                            description: 'Evento fijo para el día actual.',
                            backgroundColor: '#007bff', // Color del fondo del evento
                            borderColor: '#007bff'      // Color del borde del evento
                        }
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
                        document.getElementById('startDate').value = info.dateStr;

                        // Limpia los campos restantes (opcional)
                        document.getElementById('endDate').value = '';
                        document.getElementById('description').value = '';
                    }
                });

                // Renderiza el calendario en la página
                calendar.render();

                // Manejo del formulario de reserva
                document.getElementById('reservaForm').addEventListener('submit', function(e) {
                    e.preventDefault(); // Evita el envío normal del formulario

                    // Obtén los valores del formulario
                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;
                    const description = document.getElementById('description').value;

                    // Aquí puedes procesar los datos, enviarlos al servidor con un fetch/AJAX, etc.
                    alert(`Reserva creada:\nFecha de Inicio: ${startDate}\nFecha de Fin: ${endDate}\nDescripción: ${description}`);

                    // Cierra el modal
                    document.getElementById('reservaModal').addEventListener('hidden.bs.modal', function () {
                        const modalBackdrop = document.querySelector('.modal-backdrop');
                        if (modalBackdrop) {
                            modalBackdrop.remove();
                        }
                    });

                });
            });
        </script>
    @endpush
</x-base-layout>
