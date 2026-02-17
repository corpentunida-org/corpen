<style>
    .img-card {
        height: 180px;
        object-fit: cover;
    }

    .card {
        height: 420px;
    }

    .text-truncate-multiline {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<div class="row">
    <div class="row">
        @foreach ($inmuebles as $inmueble)
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    {{-- Carrusel --}}
                    <div id="carousel{{ $inmueble->id }}" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                        <div class="carousel-inner">
                            @foreach ($inmueble->fotosrel as $key => $img)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ $img->url }}" class="d-block w-100 img-card">
                                </div>
                            @endforeach
                        </div>

                        {{-- Controles --}}
                        <button class="carousel-control-prev" type="button"
                            data-bs-target="#carousel{{ $inmueble->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button"
                            data-bs-target="#carousel{{ $inmueble->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="card-body d-flex flex-column">

                        <h5 class="card-title">{{ $inmueble->name }}</h5>

                        <p class="card-text text-muted text-truncate-multiline">
                            {{ $inmueble->description }}
                        </p>

                        <div class="mt-auto d-flex justify-content-between">
                            <a href="{{ route('apto-santamarta') }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                Ver
                            </a>

                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-success"
                                    href="{{ route('reserva.inmueble.create', $inmueble->id) }}">
                                    Reservar
                                </a>

                                @candirect('reservas.inmueble.edit')
                                <a class="btn btn-sm btn-warning">Editar</a>
                                @endcandirect
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
