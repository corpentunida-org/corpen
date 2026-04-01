<x-base-layout>
    @section('titlepage', 'Inmuebles')
    <x-success />
    <div class="main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-extrabold mb-1 pl-2">Lista de inmuebles</h1>
        @candirect('reservas.Inmueble.create')
        <div class="header-actions d-flex">
            <a href="{{ route('reserva.crudinmuebles.create') }}"
                class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-buildings me-1"></i>Agregar inmueble
            </a>
        </div>
        @endcandirect
    </div>
    @include('reserva.asociado.inmueblesindex')

    <script>
        $(document).on("click", ".btn-toggleInmueble", function(e) {
            e.preventDefault();
            let id = $(this).data("id");
            let activo = $(this).data("active");
            let accion = activo ? "inactivar" : "activar";
            let accion_text = activo ? "El inmueble dejará de estar disponible para reservas." : "El inmueble estará disponible para reservas.";
            Swal.fire({
                title: "¿Seguro que desea " + accion + " este inmueble?",
                icon: "warning",
                showCancelButton: true,
                text: accion_text,
                confirmButtonColor: activo ? "#d33" : "#28a745",
                confirmButtonText: "Sí, continuar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.value) {
                    $("#formToggleInmueble"+id).submit();
                }
            });
        });
    </script>
</x-base-layout>
