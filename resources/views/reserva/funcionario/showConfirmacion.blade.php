
<x-base-layout>
    @section('titlepage', 'ver reserva Asociado')
    <x-success />

    <div class="container mt-4" style="background-color: white; padding: 15px" id="detalle">
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Detalles de la Reserva</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <strong>ID de Reserva:</strong>
            </div>
            <div class="col-8">
                {{ $reserva->id }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Nombre del asociado:</strong>
            </div>
            <div class="col-8">
                @if( $reserva->endosada)
                    <span class="badge bg-warning">Endosado</span> - {{ $reserva->name_reserva }}
                @else
                   {{ $reserva->user->name }}
                @endif
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Contacto del asociado:</strong>
            </div>
            <div class="col-8">
                {{ $reserva->celular }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Fecha de solicitud:</strong>
            </div>
            <div class="col-8">
                {{ $reserva->fecha_solicitud }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Fecha de ingreso:</strong>
            </div>
            <div class="col-8">
                {{ $reserva->fecha_inicio }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Fecha fin:</strong>
            </div>
            <div class="col-8">
                {{ $reserva->fecha_fin }}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Estado del pago aseo:</strong>
            </div>
            <div class="col-8">
                @if($reserva->soporte_pago)
                    <span class="badge bg-success">Pagado</span>
                @else
                    <span class="badge bg-danger">Pendiente</span>
                @endif
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Soporte de Pago:</strong>
            </div>
            <div class="col-8">
                @if($reserva->soporte_pago)
                    <a href="{{ $reserva->getFile($reserva->soporte_pago) }}" target="_blank">Ver Soporte</a>
                @else
                    <span>No disponible</span>
                @endif
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-4">
                <strong>Comentario de la reserva:</strong>
            </div>
            <div class="col-8">
                {{ $reserva->comentario_reserva ?? 'No hay información adicional' }}
            </div>
        </div>
        <hr>

        @if( $reserva->res_status_id == 1)
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmarReservaModal">
                    Confirmar
                </button>

                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#notificarAjusteModal">
                    Notificar Ajuste
                </button>

                <button type="button" class="btn btn-danger">Cancelar</button>
            </div>
        </div>
        @endif

    </div>

    <!-- Modal para Confirmar Reserva -->
    <div class="modal fade" id="confirmarReservaModal" tabindex="-1" aria-labelledby="confirmarReservaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarReservaModalLabel">Confirmar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('reserva.inmueble.confirmar') }}" method="POST">
                    @csrf
                <!-- Cuerpo del Modal -->
                <div class="modal-body">

                        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                        <!-- Campo de texto organizado -->
                        <div class="mb-3">
                            <label for="comentario" class="form-label">Recomendaciones:</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="4" placeholder="Escribe algún comentario o recomendación aquí para el asociado" required></textarea>
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


    <!-- Modal para "Notificar Ajuste" -->
    <div class="modal fade" id="notificarAjusteModal" tabindex="-1" aria-labelledby="notificarAjusteModalLabel" aria-hidden="true">
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
                            <textarea class="form-control" id="comentario" name="comentario" rows="4" placeholder="Escribe el comentario aquí..." required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Enviar Notificación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('style')
        <style>
            /* Personaliza el fondo del calendario */
            #detalle {
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

            .container {
                position: static !important; /* Elimina cualquier posible contexto de apilamiento */
            }

            .nxl-container {
                filter: none !important; /* Elimina cualquier posible filtro */
            }
        </style>
    @endpush

</x-base-layout>
