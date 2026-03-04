<x-base-layout>
    @section('titlepage', 'Reserva Asociado')
    <x-success />

    <div class="row">
        <div class="col-xxl-6 col-md-12">
            <div class="card card-body">
                <h3>{{ $reserva->res_inmueble->name }}</h3>
                {{-- <span class="text-muted">Fecha ingreso: {{ $reserva->fecha_inicio }} - Fecha salida
                    {{ $reserva->fecha_fin }}</span> --}}
                <div class="text-truncate-1-line">
                    <i class="bi bi-arrow-up-right"></i>
                    <strong>Fecha Llegada:</strong>
                    <span class="text-muted">{{ $reserva->fecha_inicio }}</span>
                </div>
                <div class="text-truncate-1-line">
                    <i class="bi-arrow-down-left"></i>
                    <strong>Fecha Salida:</strong>
                    <span class="text-muted">{{ $reserva->fecha_fin }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body">                
                    <h2 class="h-1">Anexar Soporte de pago</h2>
                    <form action="{{ route('reserva.inmueble.soporte.store') }}" method="POST" enctype="multipart/form-data">                    
                        @csrf
                        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
                                @if ($reserva->soporte_pago)
                                    <a href="{{ $reserva->getFile($reserva->soporte_pago) }}" target="_blank"
                                        style="display: inline-block; text-align: center;">
                                        <img src="https://www.fecp.org.co/images/adjunto-corpen.png" width="75px"
                                            alt="Imagen de soporte">
                                        <div style="margin-top: 5px;">Ver Soporte</div>
                                    </a>
                                @else
                                    <img src="https://www.fecp.org.co/images/noadjunto-corpen.png" width="75px" class="mt-3">
                                @endif
                            </div>
                            <div class="col-12 col-md-9">
                                <div class="mb-3">
                                    <label for="archivo" class="form-label">Subir un Archivo</label>
                                    <input type="file" class="form-control " id="archivo" name="archivo" required>
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
    

</x-base-layout>
