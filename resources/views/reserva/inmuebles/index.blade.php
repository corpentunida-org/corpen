<x-base-layout>
    @section('titlepage', 'Inmuebles')
    <x-success />
    <div class="main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-extrabold mb-1 pl-2">Lista de inmuebles disponibles</h1>
        <div class="header-actions d-flex">
            <a href="{{ route('reserva.crudinmuebles.create') }}"
                class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-buildings me-1"></i>Agregar inmueble
            </a>
        </div>
    </div>
    @include('reserva.asociado.inmueblesindex')
</x-base-layout>
