<x-base-layout>
    @section('titlepage', 'Reservas lista')
    <x-success />
    <style>
        @media (max-width: 768px) {
            table thead {
                display: none;
            }

            table,
            table tbody,
            table tr,
            table td {
                display: block;
                width: 100%;
            }

            table tr {
                border-bottom: 1px solid #eee;
                padding-left: 10px;
                padding-bottom: 0;
                margin-bottom: 10px;
            }

            table td {
                display: block !important;
                text-align: left !important;
                border: none;
                padding: 2px !important;
            }

            table td a {
                display: inline-block;
            }

            table td::before {
                content: attr(data-label) ": ";
                font-weight: 600;
                color: #6c757d;
            }

            table td.text-center {
                display: block !important;
                text-align: left !important;
            }

            td[data-label="Acciones"] a {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                /*margin-top: 4px;*/
            }

            table td.sorting_1 {
                display: block !important;
                padding: 4px 0 !important;
                margin-left: 15px !important;
            }

            .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }

            .fc-toolbar-title {
                font-size: 18px;
                text-align: center;
            }

            .fc-daygrid-day-frame {
                min-height: 60px;
            }
        }

        #calendar {
            background-color: white;
            with: 100%;
            padding: 10px;
            position: static;
        }
    </style>
    @if ($historicosres != null)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header cursor-pointer" data-bs-toggle="collapse"
                    data-bs-target="#historicores_collapse_0">
                    <div class="mb-0">
                        <h5 class="fw-bold mb-1">Histórico de Reservas</h5>
                        <p class="text-muted mb-0 small">Listado detallado de todas las reservas registradas.</p>
                    </div>
                </div>
                <div class="card-body p-0 collapse show mt-0" id="historicores_collapse_0">
                    <div class="table-responsive">
                        <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                            {{-- <div class="row gy-2">                            
                                <div class="col-sm-12 col-md-12 ps-0 m-0 pb-10">
                                    <div class="dataTables_filter d-flex justify-content-md-end justify-content-center">
                                        <label class="d-inline-flex align-items-center gap-2">Buscar:
                                            <input type="text" id="buscadorTabla" class="form-control w-75"placeholder="Buscar...">
                                        </label>
                                    </div>
                                </div>
                            </div> --}}
                            <table class="table table-hover" {{-- id="tablaReservas" --}} id="customerList">
                                <thead>
                                    <tr>
                                        <th>Inmueble</th>
                                        <th>Asociado</th>
                                        <th>Fecha de Reserva</th>
                                        <th>Inicio Reserva</th>
                                        <th>Fin Reserva</th>
                                        <th>Estado</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historicosres as $hisres)
                                        <tr>
                                            <td data-label="Inmueble">
                                                <span class="text-primary">{{ $hisres->res_inmueble->name }}</span>
                                            </td>

                                            <td data-label="Cliente">
                                                <div class="fw-semibold">{{ $hisres->nid }}</div>
                                                {{ $hisres->tercero?->nom_ter ?? $hisres->user->name }}
                                            </td>

                                            <td data-label="Fecha-Reserva">
                                                {{ $hisres->created_at?->translatedFormat('j F Y') ?? '' }}
                                            </td>

                                            <td data-label="Inicio">
                                                {{ $hisres->fecha_inicio?->translatedFormat('j F Y') ?? '' }}
                                            </td>

                                            <td data-label="Fin">
                                                {{ $hisres->fecha_fin?->translatedFormat('j F Y') ?? '' }}
                                            </td>

                                            <td data-label="Estado">
                                                {{ $hisres->res_status->name }}
                                            </td>

                                            <td data-label="Acciones" class="text-center">
                                                <a class="avatar-text avatar-md" data-toggle="tooltip"
                                                    data-title="Ver detalle"
                                                    href="{{ route('reserva.inmueble.confirmacion.show', $hisres->id) }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                {{-- <a href="#" href="{{ route('reserva.reserva.edit', $reserva->id) }}" class="btn btn-sm btn-warning"> <i class="bi bi-pencil-square"></i> </a> --}}
                                                {{-- <form action="{{ route('reserva.reserva.destroy', $reserva->id) }}" method="POST"> @csrf @method('DELETE') <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta reserva?')"> <i class="bi bi-trash"></i> </button> </form> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- <div class="card-footer">
                    <span id="contador" class="text-muted"></span>
                    {{ $historicosres->links() }}
                </div> --}}
            </div>
        </div>
    @endif

    <div class="col-lg-12">
        <div class="card" data-scrollbar-target="#psScrollbarInit">
            <div class="card-header">
                <h5 class="fw-bold mb-1">Reservas Calendario</h5>
                <p class="text-muted mb-0 small">
                    Calendario detallado de reservas activas.
                </p>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    locale: 'es',
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    contentHeight: 'auto',
                    expandRows: true,
                    handleWindowResize: true,
                    initialDate: '{{ optional($historicosres->min('fecha_inicio'))?->format('Y-m-d') }}',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth'
                    },
                    events: [
                        @foreach ($historicosres as $r)                        
                            {
                                title: '{{ $r->res_status_id == 4 ? 'RESERVA CANCELADA ' . $r->res_inmueble->name : 'RESERVA ' . $r->res_inmueble->name }}',
                                start: '{{ \Carbon\Carbon::parse($r->fecha_inicio)->format('Y-m-d') }}',
                                end: '{{ \Carbon\Carbon::parse($r->fecha_fin)->addDay()->format('Y-m-d') }}',
                                color: '{{ $r->res_status_id == 4 ? '#ffb3b3' : ($r->res_inmueble_id == 1 ? '#c8b6ff' : '#b6e3ff') }}',
                                textColor: '#2b1a55',
                                aspectRatio: 1.35,
                                extendedProps: {
                                    id: '{{ $r->id }}',
                                    apto: '{{ $r->res_inmueble->name ?? '' }}',
                                    fecha_inicio: '{{ $r->fecha_inicio }}',
                                    fecha_fin: '{{ $r->fecha_fin }}',
                                    usuario: '{{ $r->nid }} - {{ $r->user->name ?? '' }}',
                                    telefono: '{{ $r->celular }} - {{ $r->celular_respaldo }}',
                                    celular: '{{ $r->celular }}',
                                    celular_respaldo: '{{ $r->celular_respaldo }}'
                                }
                            },
                        @endforeach
                    ],
                    eventClick: function(info) {

                        let inicio = new Date(info.event.extendedProps.fecha_inicio);
                        let fin = new Date(info.event.extendedProps.fecha_fin);

                        let opciones = {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        };

                        let fechaInicio = inicio.toLocaleDateString('es-ES', opciones);
                        let fechaFin = fin.toLocaleDateString('es-ES', opciones);
                        const telefono = `${info.event.extendedProps.celular} ${info.event.extendedProps.celular_respaldo ? ' - ' + info.event.extendedProps.celular_respaldo : ''}`;
                        Swal.fire({
                            title: 'Detalle de Reserva',
                            icon: 'info',
                            html: `
                            <p><b>ID Reserva:</b> ${info.event.extendedProps.id}</p>
                            <p><b>Apartamento:</b> ${info.event.extendedProps.apto}</p>
                            <p><b>Usuario:</b> ${info.event.extendedProps.usuario}</p>
                            <p><b>Teléfonos:</b> ${telefono}</p>                            
                            <p><b>Fecha Inicio:</b> ${fechaInicio} </p>
                            <p><b>Fecha Fin:</b> ${fechaFin}</p>
                        `,
                            confirmButtonText: 'Cerrar'
                        });
                    }
                });
                calendar.render();
                window.addEventListener('resize', function() {
                    calendar.updateSize();
                });
            }
        });
    </script>
</x-base-layout>
