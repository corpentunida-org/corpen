<x-base-layout>
    @section('titlepage', 'Reservas Confirmadas Asociado')
    <x-success />
    <style>
        #calendar {
            background-color: white;
            padding: 10px;
            position: static;
            /*z-index: auto;*/
        }
    </style>
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="customerList">
                        <thead>
                            <tr>
                                <th>Inmueble</th>
                                <th>Asociado</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Estado</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reservas as $reserva)
                                <tr>
                                    <td>
                                        <div class="fw-semibold mb-1"></div>
                                        <div class="d-flex gap-3">
                                            <a href="javascript:void(0);"
                                                class="hstack gap-1 fs-11 fw-normal text-primary">
                                                <span>{{ $reserva->res_inmueble->name }}</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($reserva->endosada)
                                            <div class="fw-semibold mb-1">{{ $reserva->name_reserva }}</div>
                                        @else
                                            <span class="text-muted">{{ $reserva->nid }}</span>
                                            <div class="fw-semibold mb-1">{{ $reserva->user->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $reserva->fecha_inicio }}
                                    </td>
                                    <td>
                                        {{ $reserva->fecha_fin }}
                                    </td>
                                    <td>
                                        <div class="btn btn-sm bg-soft-teal text-teal d-inline-block">
                                            {{ $reserva->res_status->name }}</div>
                                    </td>
                                    <td>
                                        <div class="hstack gap-3 justify-content-end">
                                            <a href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}"
                                                class="fw-bold text-primary">Agregar Comentario</a>
                                            {{-- -- <a href="{{ route('reserva.inmueble.confirmacion.show', $reserva->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-grid"></i>
                                            </a>
                                            <a href="#"  href="{{ route('reserva.reserva.edit', $reserva->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a> -- --}}
                                            <form action="{{ route('reserva.reserva.destroy', $reserva->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Está seguro de eliminar esta reserva?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card" data-scrollbar-target="#psScrollbarInit">
            <div class="card-body p-4">
                <div id="calendar" class="p-4"></div>
            </div>
        </div>
    </div>

    </div>
    <script>
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                height: "auto",
                locale: 'es',
                contentHeight: "auto",
                expandRows: false,

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
                            start: '{{ \Carbon\Carbon::parse($r->fecha_inicio)->format('Y-m-d') }}',
                            end: '{{ \Carbon\Carbon::parse($r->fecha_fin)->addDay()->format('Y-m-d') }}',
                            color: '#c8b6ff',
                            textColor: '#2b1a55',
                            extendedProps: {
                                id: '{{ $r->id }}',
                                apto: '{{ $r->res_inmueble->name }}',
                                fecha_inicio: '{{ $r->fecha_inicio }}',
                                fecha_fin: '{{ $r->fecha_fin }}',
                                usuario: '{{ $r->nid }} - {{ $r->user->name }}',
                                telefono: '{{ $r->celular }} - {{ $r->celular_respaldo }}'
                            }
                        },                    
                    @endforeach
                ],
                eventClick: function(info) {

                    let inicio = new Date(info.event.extendedProps.fecha_inicio);
                    let fin = new Date(info.event.extendedProps.fecha_fin);

                    let opciones = {day: 'numeric',month: 'long',year: 'numeric'};

                    let fechaInicio = inicio.toLocaleDateString('es-ES', opciones);
                    let fechaFin = fin.toLocaleDateString('es-ES', opciones);

                    Swal.fire({
                        title: 'Detalle de Reserva',
                        icon: 'info',
                        html: `
                            <p><b>ID Reserva:</b> ${info.event.extendedProps.id}</p>
                            <p><b>Apartamento:</b> ${info.event.extendedProps.apto}</p>
                            <p><b>Usuario:</b> ${info.event.extendedProps.usuario}</p>
                            <p><b>Teléfonos:</b> ${info.event.extendedProps.telefono}</p>                            
                            <p><b>Fecha Inicio:</b> ${fechaInicio} </p>
                            <p><b>Fecha Fin:</b> ${fechaFin}</p>
                        `,
                        confirmButtonText: 'Cerrar'
                    });

                }
            });
            calendar.render();
        } 
    </script>
</x-base-layout>
