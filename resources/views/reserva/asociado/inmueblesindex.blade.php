<div class="row">
    <div class="col-xxl-3 col-md-6">
        @foreach ($inmuebles as $inmueble)
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <h5 class="fs-4">{{ $inmueble->name }}</h5>
                        <span class="text-muted">{{ $inmueble->description }}</span><br>
                        <a href="{{ route('apto-santamarta') }}" target="_blank">Ver apartamento</a>
                    </div>
                    <div class="btn btn-md btn-success">
                        <a href="{{ route('reserva.inmueble.create', $inmueble->id) }}"
                            style="color: white">Reservar</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
