{{-- resources/views/rsv/admin/partials/tab-calendario.blade.php --}}

<div class="card border-0 shadow-sm rounded-4" data-scrollbar-target="#psScrollbarInit">
    <div class="card-body p-4 d-flex flex-column" style="min-height: 600px;">

        <!-- LEYENDA DE COLORES Y FRANJAS -->
        <div class="d-flex flex-wrap gap-3 mb-3 pb-3 border-bottom small fw-medium text-muted">
            <span class="d-flex align-items-center gap-2">
                <div class="rounded shadow-sm" style="width:16px;height:16px;background:#c8b6ff;"></div> Aprobada
            </span>
            <span class="d-flex align-items-center gap-2">
                <div class="rounded shadow-sm" style="width:16px;height:16px;background:repeating-linear-gradient(45deg, #fff3cd, #fff3cd 4px, #ffe8a1 4px, #ffe8a1 8px);"></div> Pendiente
            </span>
            <span class="d-flex align-items-center gap-2">
                <div class="rounded shadow-sm" style="width:16px;height:16px;background:repeating-linear-gradient(45deg, #cce5ff, #cce5ff 4px, #b8daff 4px, #b8daff 8px);"></div> Con Endoso
            </span>
            <span class="d-flex align-items-center gap-2">
                <div class="rounded shadow-sm" style="width:16px;height:16px;background:repeating-linear-gradient(45deg, #f8d7da, #f8d7da 4px, #f1b0b7 4px, #f1b0b7 8px);"></div> Cancelada
            </span>
            <span class="d-flex align-items-center gap-2">
                <div class="rounded shadow-sm" style="width:16px;height:16px;background:#dc3545;"></div> Mantenimiento
            </span>
        </div>

        <div class="flex-grow-1 border rounded-3 bg-white p-2">
            <div id="calendario-global"></div>
        </div>

    </div>
</div>

@push('style')
<style>
    #calendario-global { background-color: white; padding: 10px; position: static; }
    .fc .fc-daygrid-day.fc-day-today, .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-frame { background-color: #fff9e2 !important; }
    .fc-dayGridMonth-view .fc-scrollgrid-sync-table { height: auto !important; }
    .fc-dayGridMonth-view .fc-daygrid-day-frame { min-height: 100px !important; padding: 2px !important; }
    .fc-multimonth-month { padding: 0.5rem !important; }
    .fc .fc-multimonth-daygrid-table { min-height: auto !important; }
    .fc-event-title { font-weight: 500; }
    .fc-event { cursor: pointer; }
    .modal-backdrop { position: fixed; width: 100vw; height: 100vh; z-index: 1040 !important; }
    .modal { z-index: 1055 !important; }

    /* Franjas CSS */
    .evento-aprobada { background-color: #c8b6ff !important; border-color: #a482ff !important; color: #2b1a55 !important; }
    .evento-franjas-pendiente { background: repeating-linear-gradient(45deg, #fff3cd, #fff3cd 6px, #ffe8a1 6px, #ffe8a1 12px) !important; border-color: #ffc107 !important; color: #856404 !important; }
    .evento-franjas-endoso { background: repeating-linear-gradient(45deg, #cce5ff, #cce5ff 6px, #b8daff 6px, #b8daff 12px) !important; border-color: #0d6efd !important; color: #004085 !important; }
    .evento-franjas-cancelada { background: repeating-linear-gradient(45deg, #f8d7da, #f8d7da 6px, #f1b0b7 6px, #f1b0b7 12px) !important; border-color: #dc3545 !important; color: #721c24 !important; text-decoration: line-through; opacity: 0.8; }
    .evento-bloqueo { background-color: #dc3545 !important; border-color: #b02a37 !important; color: #ffffff !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendario-global');

        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                height: "auto",
                contentHeight: "auto",
                expandRows: false,
                initialView: 'dayGridMonth',
                multiMonthMaxColumns: 3,
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'multiMonthYear,dayGridMonth,timeGridWeek'
                },
                buttonText: { year: 'Año', month: 'Mes', week: 'Semana', today: 'Hoy' },
                eventDisplay: 'block',

                events: function(fetchInfo, successCallback, failureCallback) {
                    const start = fetchInfo.startStr.split('T')[0];
                    const end = fetchInfo.endStr.split('T')[0];

                    const urlReservas = `/rsv/reservas?fecha_desde=${start}&fecha_hasta=${end}&per_page=2000`;
                    const urlBloqueos = `/rsv/bloqueos-calendario?fecha_desde=${start}&fecha_hasta=${end}&per_page=2000`;

                    // Usamos SOLO Accept json, para no activar el ->ajax() del controlador
                    const opts = { headers: { 'Accept': 'application/json' } };

                    Promise.all([
                        fetch(urlReservas, opts).then(res => res.json()),
                        fetch(urlBloqueos, opts).then(res => res.json())
                    ])
                    .then(([resReservas, resBloqueos]) => {
                        let eventos = [];

                        // 🚀 EXTRAE LA DATA A PRUEBA DE BALAS (Soporta paginación o data directa)
                        let arrayReservas = resReservas.data && resReservas.data.data ? resReservas.data.data : resReservas.data;
                        let arrayBloqueos = resBloqueos.data && resBloqueos.data.data ? resBloqueos.data.data : resBloqueos.data;

                        // Mapeo de Reservas
                        if (Array.isArray(arrayReservas)) {
                            arrayReservas.forEach(reserva => {
                                if(!reserva.fecha_inicio || !reserva.fecha_fin) return;

                                let endFormat = new Date(reserva.fecha_fin);
                                endFormat.setDate(endFormat.getDate() + 1); // Exclusivo en FullCalendar

                                let nombreEstado = reserva.status ? (reserva.status.nombre || reserva.status.name || '').toLowerCase() : '';
                                let tieneEndoso = (reserva.historial_endosos && reserva.historial_endosos.length > 0) ||
                                                  (reserva.historialEndosos && reserva.historialEndosos.length > 0);

                                let cssClass = 'evento-aprobada';
                                let icono = 'bi-calendar-check';

                                if (nombreEstado.includes('cancelad')) { cssClass = 'evento-franjas-cancelada'; icono = 'bi-calendar-x'; }
                                else if (tieneEndoso || nombreEstado.includes('endos')) { cssClass = 'evento-franjas-endoso'; icono = 'bi-arrow-left-right'; }
                                else if (nombreEstado.includes('pendient') || nombreEstado.includes('pre')) { cssClass = 'evento-franjas-pendiente'; icono = 'bi-hourglass-split'; }

                                eventos.push({
                                    id: 'rsv_' + reserva.id,
                                    title: `${reserva.inmueble ? reserva.inmueble.name : 'Apto'} (${reserva.codigo_reserva})`,
                                    start: reserva.fecha_inicio.split(' ')[0],
                                    end: endFormat.toISOString().split('T')[0],
                                    allDay: true,
                                    classNames: [cssClass],
                                    extendedProps: {
                                        tipo: 'reserva',
                                        icono: icono,
                                        codigo: reserva.codigo_reserva,
                                        estadoVisual: nombreEstado.toUpperCase(),
                                        inmueble: reserva.inmueble ? reserva.inmueble.name : 'Apto',
                                        usuario: reserva.user ? reserva.user.name : 'Titular'
                                    }
                                });
                            });
                        }

                        // Mapeo de Bloqueos
                        if (Array.isArray(arrayBloqueos)) {
                            arrayBloqueos.forEach(bloqueo => {
                                if(!bloqueo.fecha_inicio || !bloqueo.fecha_fin) return;

                                let endFormat = new Date(bloqueo.fecha_fin.split(' ')[0]);
                                endFormat.setDate(endFormat.getDate() + 1);

                                eventos.push({
                                    id: 'blq_' + bloqueo.id,
                                    title: 'Mantenimiento: ' + (bloqueo.inmueble ? bloqueo.inmueble.name : ''),
                                    start: bloqueo.fecha_inicio.split(' ')[0],
                                    end: endFormat.toISOString().split('T')[0],
                                    allDay: true,
                                    classNames: ['evento-bloqueo'],
                                    extendedProps: {
                                        tipo: 'bloqueo',
                                        icono: 'bi-wrench-adjustable',
                                        motivo: bloqueo.motivo,
                                        inmueble: bloqueo.inmueble ? bloqueo.inmueble.name : 'N/A'
                                    }
                                });
                            });
                        }

                        successCallback(eventos);
                    }).catch(error => { console.error("Error Fetch:", error); failureCallback(error); });
                },

                eventContent: function(arg) {
                    let icon = arg.event.extendedProps.icono;
                    if (arg.view.type === 'multiMonthYear') { return { html: `<div class="p-0 text-truncate" style="font-size: 0.7rem;"><i class="bi ${icon}"></i> ${arg.event.title}</div>` }; }
                    return { html: `<div class="p-1 text-truncate"><i class="bi ${icon} me-1 ms-1"></i> ${arg.event.title}</div>` };
                },

                eventClick: function(info) {
                    let props = info.event.extendedProps;
                    let esBloqueo = props.tipo === "bloqueo";

                    let icono = esBloqueo ? "warning" : "info";
                    let colorBtn = esBloqueo ? "#dc3545" : "#6f42c1";
                    let titulo = esBloqueo ? "Mantenimiento / Bloqueo" : "Detalle de Reserva";

                    let opciones = { day: 'numeric', month: 'long', year: 'numeric' };
                    let fechaInicio = new Date(info.event.start).toLocaleDateString('es-ES', opciones);

                    let fechaFinObj = info.event.end ? new Date(info.event.endStr) : new Date(info.event.startStr);
                    if (info.event.end) fechaFinObj.setDate(fechaFinObj.getDate() - 1);
                    let fechaFin = fechaFinObj.toLocaleDateString('es-ES', opciones);

                    let extras = esBloqueo
                        ? `<p><b>Motivo:</b> ${props.motivo}</p>`
                        : `<p><b>Código:</b> ${props.codigo}</p>
                           <p><b>Estado:</b> <span class="badge bg-secondary">${props.estadoVisual}</span></p>
                           <p><b>Titular:</b> ${props.usuario}</p>`;

                    Swal.fire({
                        title: titulo, icon: icono,
                        html: `
                            <div class="text-start mt-3" style="font-size: 1rem;">
                                <p><b>Inmueble:</b> ${props.inmueble}</p>
                                ${extras}
                                <p><b>Ingreso:</b> ${fechaInicio}</p>
                                <p><b>Salida:</b> ${fechaFin}</p>
                            </div>
                        `,
                        confirmButtonColor: colorBtn, confirmButtonText: 'Cerrar'
                    });
                },

                dayCellDidMount(arg) {
                    const today = new Date(); today.setHours(0, 0, 0, 0);
                    const cellDate = new Date(arg.date); cellDate.setHours(0, 0, 0, 0);
                    if (cellDate < today) {
                        arg.el.style.backgroundColor = "#f8f9fa";
                        arg.el.style.opacity = "0.7";
                    }
                }
            });

            calendar.render();
            const calendarTab = document.querySelector('button[data-bs-target="#calendario"]');
            if (calendarTab) { calendarTab.addEventListener('shown.bs.tab', () => calendar.updateSize()); }
        }
    });
</script>
@endpush
