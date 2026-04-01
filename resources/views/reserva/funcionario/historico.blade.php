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
                                                {{ $hisres->created_at->translatedFormat('j F Y') }}
                                            </td>

                                            <td data-label="Inicio">
                                                {{ $hisres->fecha_inicio->translatedFormat('j F Y') }}
                                            </td>

                                            <td data-label="Fin">
                                                {{ $hisres->fecha_fin->translatedFormat('j F Y') }}
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
            <div class="card stretch stretch-full">
                <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#calendarCollapse">
                    <h5 class="fw-bold mb-1">Reservas Calendario</h5>
                    <p class="text-muted mb-0 small">
                        Calendario detallado de reservas activas.
                    </p>
                </div>
                <hr class="m-0">

                <div class="card-body d-flex flex-column p-0">
                    <div id="calendar" class="flex-fill"></div>
                </div>
            </div>
    </div>

    <script>
        $('#customerList').DataTable({
            columnDefs: [{
                targets: '_all',
                className: 'text-start'
            }]
        });

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                selectable: true,
                editable: true,
                height: '100%',
                expandRows: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                // Click en día para agregar evento
                dateClick: function(info) {
                    const title = prompt("Nombre del evento:");
                    if (title) {
                        calendar.addEvent({
                            title: title,
                            start: info.dateStr,
                            allDay: true,
                            height: 600,
                        });
                    }
                },

            });

            calendar.render();
        });
    </script>
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {

            const buscador = document.getElementById('buscadorTabla');
            const filas = document.querySelectorAll('#tablaReservas tbody tr');
            const contador = document.getElementById('contador');

            function filtrarTabla() {
                let filtro = buscador.value.toLowerCase();
                let visibles = 0;

                filas.forEach(fila => {
                    let texto = fila.innerText.toLowerCase();

                    if (texto.includes(filtro)) {
                        fila.style.display = '';
                        visibles++;
                    } else {
                        fila.style.display = 'none';
                    }
                });

                contador.innerText = visibles + ' resultados';
            }

            buscador.addEventListener('keyup', filtrarTabla);

            // Inicializar contador
            contador.innerText = filas.length + ' resultados';


            // 🔥 TOOLTIPS (Bootstrap 5)
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });

        });
    </script> --}}
</x-base-layout>
