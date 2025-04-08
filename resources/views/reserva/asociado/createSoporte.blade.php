
<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />

    <div class="row">
        <div class="col-xxl-3 col-md-6">

                <div class="card card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-12">
                            <h5 class="fs-4">{{ $reserva->res_inmueble->name }}</h5>
                            <span class="text-muted">Fecha ingreso: {{ $reserva->fecha_inicio }} -  Fecha salida {{ $reserva->fecha_fin }}</span>
                        </div>
                    </div>
                </div>

        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div style="padding: 20px">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Soporte de pago</h1>
                    <form action="{{ route('reserva.inmueble.soporte.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" style="padding: 15px">
                        @csrf

                        <!-- Etiqueta para seleccionar el archivo -->
                        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                        <div class="row align-items-center">

                            <div class="col-3 text-center">
                                @if($reserva->soporte_pago)
                                    <a href="{{ $reserva->getFile($reserva->soporte_pago) }}" target="_blank" style="display: inline-block; text-align: center;">
                                        <img src="https://www.fecp.org.co/images/adjunto-corpen.png" width="75px" alt="Imagen de soporte">
                                        <div style="margin-top: 5px;">Ver Soporte</div>
                                    </a>

                                @else
                                    <img src="https://www.fecp.org.co/images/noadjunto-corpen.png" width="75px">
                                @endif
                            </div>
                            <div class="col-9">
                                <div class="mb-3">
                                    <label for="archivo" class="form-label">Subir un Archivo</label>
                                    <input type="file" class="form-control " id="archivo" name="archivo" required >
                                </div>
                            </div>
                        </div>


                        <!-- Botón para enviar el formulario -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success">
                                Subir Archivo
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    </div>

</x-base-layout>
