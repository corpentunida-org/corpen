<x-base-layout>
    @section('titlepage', 'ver reserva Asociado')
    <x-success />
    @php
        $btnFin = $reserva->fecha_fin->isPast();
    @endphp
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
    <div class="col-lg-12 card" id="detalle">
        <div class="card-header d-md-flex align-items-center justify-content-between">
            <h5 class="fw-bold">Detalle de la Reserva #{{ $reserva->id }}</h5>
            @if($reserva->res_status_id != 3)
            <button type="button" class="btn {{ !$btnFin ? 'btn-success' : 'btn-danger' }}" data-bs-toggle="modal"
                data-bs-target="#confirmarReservaModal">
                <i class="fas fa-comment me-2"></i> {{ !$btnFin ? 'Nuevo Comentario' : 'Finalizar Reserva' }}
            </button>
            @endif             
        </div>
        <div class="card-body ">
            <div class="table-responsive m-0">
                <table class="table table-hover">
                    <tbody class="text-center">
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Asociado:</td>
                            <td class="text-start">
                                @if ($reserva->endosada)
                                    <span class="badge bg-warning">Endosado</span> - {{ $reserva->name_reserva }}
                                @else
                                    <span class="text-muted">{{ $reserva->nid }} - {{ $reserva->user->name }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Contacto del asociado:</td>
                            <td class="text-start"><span class="text-muted">{{ $reserva->celular }} -
                                    {{ $reserva->celular_respaldo }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Fecha de solicitud:</td>
                            <td class="text-start"><span
                                    class="text-muted">{{ $reserva->fecha_solicitud->format('d M, Y') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Fecha Estadia:</td>
                            <td class="text-start"><span
                                    class="text-muted">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d M, Y') }}
                                    <span class="fw-bold">-</span>
                                    {{ $reserva->fecha_fin->format('d M, Y') }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Estado del pago aseo:</td>
                            <td class="text-start">
                                @if ($reserva->soporte_pago)
                                    <span class="badge bg-success">Pagado</span>
                                @else
                                    <span class="badge bg-danger">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Soporte de Pago:</td>
                            <td class="text-start">
                                @if ($reserva->soporte_pago)
                                    <a href="{{ $reserva->getFile($reserva->soporte_pago) }}" target="_blank">
                                        Ver Soporte
                                    </a>
                                @else
                                    <span class="text-muted">No disponible</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-medium text-dark text-start ps-4">Comentario de la reserva:</td>
                            <td class="text-start">
                                <span class="text-muted">
                                    {{ $reserva->comentario_reserva ?? 'No hay información adicional.' }}
                                </span>
                            </td>
                        </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#rescomments_collapse_0">
                <div class="mb-0">
                    <h5 class="fw-bold mb-1">Comentarios sobre esta reserva</h5>
                </div>
            </div>
            <div class="card-body p-0 collapse show mt-0" id="rescomments_collapse_0">
                <div class="recent-activity p-4 pb-0">
                    <ul class="list-unstyled activity-feed">
                        @foreach ($reserva->comments as $comment)
                            <li class="d-flex justify-content-between feed-item feed-item-primary">
                                <div>
                                    <span
                                        class="text-truncate-1-line text lead_date">{{ $comment->created_at->format('d M, Y H:i') }}</span>
                                    <span class="lead_date">{{ $comment->description }}</span>
                                </div>
                                <div class="ms-3 d-flex gap-2 align-items-center">
                                    <span class="badge bg-soft-primary text-primary">{{ $comment->user->name }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if($reserva->res_status_id != 3)
    <!-- Modal para Confirmar Reserva -->
        <div class="modal fade" id="confirmarReservaModal" tabindex="-1" aria-labelledby="confirmarReservaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Encabezado del Modal -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmarReservaModalLabel">{{ !$btnFin ? 'Confirmar' : 'Finalizar' }}
                            Reserva</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('reserva.inmueble.confirmar') }}" method="POST">
                        @csrf
                        <!-- Cuerpo del Modal -->
                        <div class="modal-body">
                            <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                            <!-- Campo de texto organizado -->
                            <div class="mb-3">
                                <label for="comentario"
                                    class="form-label">{{ !$btnFin ? 'Recomendaciones' : 'Comentarios' }}</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="4"
                                    placeholder="Escribe algún comentario o recomendacion para el asociado" required></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="notificar" name="notificar" value="1" checked>
                                <label class="form-check-label fw-semibold" for="notificar">Notificar al asociado</label>
                            </div>
                            @if ($btnFin)
                                <div class="rating-section text-center">
                                    <label class="form-label">
                                        Califique al asociado del 1 al 5, dando click sobre la estrella:
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
                            @endif
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
    @endif


    <!-- Modal para "Notificar Ajuste" -->
    {{-- <div class="modal fade" id="notificarAjusteModal" tabindex="-1" aria-labelledby="notificarAjusteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="notificarAjusteModalLabel">Agregar Comentario del Ajuste</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                    <form action="{{ route('reserva.inmueble.notificar.ajuste') }}" method="POST" id="notificar">
                        @csrf
                        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                        <div class="mb-3">
                            <label for="comentario" class="form-label">Comentario:</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="4"
                                placeholder="Escribe el comentario aquí..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Enviar Notificación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {

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

        });
    </script>

</x-base-layout>
