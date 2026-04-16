<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />
    <style>
        .rating-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .rating-radio {
            display: none;
        }

        .star {
            font-size: 30px;
            color: #ccc;
            transition: all 0.2s ease;
        }

        .number {
            font-size: 10px;
            font-weight: 600;
            color: #999;
            transition: all 0.2s ease;
        }

        .rating-item:hover .star {
            transform: scale(1.15);
        }

        .rating-disabled {
            pointer-events: none;
            opacity: 0.4;
        }
    </style>
    @if ($reservas->count() != 0)
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <div class="hstack justify-content-between">
                        <div>
                            <h2 class="fw-extrabold">Mis Reservas</h2>
                            @if ($reservas->contains('res_status_id', 1))
                                <div class="alert alert-dismissible p-2 d-flex alert-soft-danger-message" role="alert">
                                    <div class="p-2">
                                        <p class="fw-bold text-truncate-1-line">IMPORTANTE!</p>
                                        <p class="fs-12 fw-medium">Las reservas que se encuentren en estado
                                            <strong>RESERVADA </strong>deberán adjuntar el soporte de pago mediante el
                                            botón
                                            azul dentro de los TRES (3) DÍAS siguientes a la fecha de creación de la
                                            reserva.<strong>
                                                EN CASO DE NO ANEXAR el comprobante dentro de este plazo, </strong> la
                                            reserva será <strong>
                                                cancelada automáticamente</strong> y el espacio será asignado a otro
                                            asociado.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Inmueble</th>
                                    <th>Fecha Inicio - Fin</th>
                                    <th>Comentario</th>
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
                                        <td>{{ $reserva->fecha_inicio }} a {{ $reserva->fecha_fin }}</td>
                                        <td>{{ $reserva->comentario_reserva ?: ' ' }}</td>
                                        <td>
                                            <div class="fw-semibold mb-1">{{ $reserva->res_status->name }}</div>
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 justify-content-end">
                                                @if ($reserva->res_status_id != 4)
                                                    @if ($reserva->soporte_pago == null || empty($reserva->soporte_pago))
                                                        <a href="{{ route('reserva.inmueble.soporte.create', $reserva->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bi bi-cloud-upload me-2"></i> Anexar comprobante
                                                        </a>
                                                    @elseif($reserva->res_status_id == 3)
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            data-bs-toggle="modal" data-bs-target="#calificarfinreserva"
                                                            data-reserva-id="{{ $reserva->id }}">
                                                            <i class="bi bi-star me-2"></i> Calificar
                                                        </button>
                                                    @else
                                                        <a href="{{ $reserva->getFile($reserva->soporte_pago) }}"
                                                            class="btn btn-sm btn-primary" target="_blank">
                                                            <i class="bi bi-paperclip me-2"></i> Ver comprobante
                                                        </a>
                                                    @endif
                                                @endif
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
    @endif

    @include('reserva.asociado.inmueblesindex')

    <div class="modal fade" id="calificarfinreserva" tabindex="-1" aria-labelledby="calificarfinreservaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="calificarfinreservaModalLabel">
                        Calificar mi Estadía</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('reserva.inmueble.resenia') }}" method="POST">
                    @csrf
                    <!-- Cuerpo del Modal -->
                    <div class="modal-body">
                        <input type="hidden" name="reserva_id" id="reserva_id">
                        <!-- Campo de texto organizado -->
                        <div class="mb-3">
                            <label for="comentario" class="form-label">Califica el servicio</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="4"
                                placeholder="Escribe algún comentario del servicio y/o del inmueble" required></textarea>
                        </div>
                        <div class="rating-section text-center">
                            <label class="form-label">
                                Califique el servicio del 1 al 5, dando click sobre la estrella:
                            </label>
                            <div class="d-flex justify-content-center gap-4 rating-group">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="rating-item text-center">
                                        <input type="radio" class="rating-radio" name="calificacion"
                                            value="{{ $i }}" required>
                                        <div class="star">★</div>
                                        <div class="number">{{ $i }}</div>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Pie del Modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Instrucciones de Pago -->
    <div class="modal fade" id="modalComoPagar" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Opciones de Pago</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-success">Instrucciones de Pago</h5>
                            <p>El valor de la reserva es de <b>$250.000</b></p>

                            <p>
                                El pago debe realizarse mediante <b>consignación</b> a la
                                <b>cuenta de ahorros N° 032386054</b> del Banco de Bogotá.
                            </p>

                            <p>
                                Utilice el <b>formato de recaudo nacional</b> y diligencie la información:
                            </p>
                            <ul>
                                <li><b>Referencia 1:</b> Cédula del pastor.</li>
                                <li><b>Referencia 2:</b> Número de celular del pastor.</li>
                            </ul>
                            <p>
                                Una vez realizada la consignación, debe
                                <b>subir el comprobante de pago</b> en el portal donde realizó la reserva
                                para que el pago pueda ser verificado.
                            </p>
                            <p>
                                <a href="https://app.corpentunida.org.co/reservas/reserva" target="_blank">
                                    https://app.corpentunida.org.co/reservas/reserva
                                </a>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h5 class="text-success">Métodos Alternativos</h5>
                            <h6 class="text-primary">Pagos en Línea</h6>
                            <p>
                                <a href="https://www.avalpaycenter.com/wps/portal/portal-de-pagos/web/pagos-aval/resultado-busqueda/realizar-pago?idConv=00021874&origen=buscar"
                                    target="_blank">Click aquí para portal pagos en línea</a>
                            </p>
                            <p>Seleccione el método de pago disponible.</p>
                            <p>
                                En el campo <b>referencia o concepto de pago</b>, escriba:
                                <b>Pago reserva</b>.
                            </p>
                            <p>
                                Complete el proceso y adjunte el comprobante de pago
                                al aplicativo donde realizó la reserva.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                        ¡Entendido!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            //calificacion
            function paintStars(section, value) {
                const items = section.querySelectorAll('.rating-item');

                items.forEach(item => {
                    const radio = item.querySelector('.rating-radio');
                    const star = item.querySelector('.star');
                    const number = item.querySelector('.number');

                    if (parseInt(radio.value) <= parseInt(value)) {
                        star.style.color = '#ffc107'; // amarillo bootstrap
                        number.style.color = '#dc3545'; // rojo bootstrap
                        star.style.transform = 'scale(1.2)';
                        number.style.transform = 'scale(1.1)';
                    } else {
                        star.style.color = '#ccc';
                        number.style.color = '#999';
                        star.style.transform = 'scale(1)';
                        number.style.transform = 'scale(1)';
                    }
                });
            }

            function initRatings(section) {
                const items = section.querySelectorAll('.rating-item');

                items.forEach(item => {
                    const radio = item.querySelector('.rating-radio');
                    const star = item.querySelector('.star');

                    star.addEventListener('click', () => {
                        if (radio.disabled) return;
                        radio.checked = true;
                        paintStars(section, radio.value);
                    });

                    radio.addEventListener('change', () => {
                        paintStars(section, radio.value);
                    });
                });
            }

            const ratingSection = document.querySelector('.rating-section');
            if (ratingSection) {
                initRatings(ratingSection);
            }

            $('#calificarfinreserva').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var reservaId = button.data('reserva-id');
                $('#reserva_id').val(reservaId);
            });

            //alert reservadas
            window.tieneReservaPendiente = {{ $reservas->contains('res_status_id', 1) ? 'true' : 'false' }};
            if (window.tieneReservaPendiente) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    text: 'Tienes reservas pendientes en estado RESERVADA. Recuerda anexar el comprobante de pago dentro de los próximos 3 días para evitar la cancelación automática.',
                    confirmButtonText: 'Entendido',
                    showCancelButton: true,
                    confirmButtonText: 'Entendido',
                    cancelButtonText: '¿Cómo pagar?',
                    confirmButtonColor: '#3454D1',
                    cancelButtonColor: '#F59E0B'

                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        var modal = new bootstrap.Modal(document.getElementById('modalComoPagar'));
                        modal.show();
                    }
                });
            }
        });
    </script>
</x-base-layout>
