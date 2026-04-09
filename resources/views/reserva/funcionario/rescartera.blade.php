<x-base-layout>
    @section('titlepage', 'Verificar Comprobantes')
    <x-success />
    <div class="card stretch stretch-full">
        <a class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <h5 class="mb-0">Reservadas sin soporte de pago</h5>
            <label class="d-flex align-items-center gap-2 ms-auto">
                Search:
                <input placeholder="Search..." class="form-control form-control-sm" id="buscadorReservas" type="text">
            </label>
        </a>
        <div class="card-body" style="">
            @if ($reservas->count() == 0)
                <div class="text-center">
                    <i class="bi bi-check2-circle fs-1 text-success mb-3"></i>
                    <p class="text-muted">No hay reservas pendientes de verificación de pago</p>
                </div>
            @else
                <ul class="list-unstyled mb-0" id="listaReservas">
                    @foreach ($reservas as $res)
                        <li class="p-3 mb-3 border border-dashed rounded-3 item-reserva">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3 me-3">
                                    <div class="custom-control custom-checkbox me-2"> <input
                                            class="custom-control-input checkconfirmres"
                                            id="checkReserva_{{ $res->id }}" type="checkbox"
                                            data-id="{{ $res->id }}"> <label class="custom-control-label c-pointer"
                                            for="checkReserva_{{ $res->id }}"></label> </div> <input type="hidden"
                                        class="txt-contact" value="{{ $res->celular }} - {{ $res->celular_respaldo }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="lh-base txt-nid">{{ $res->nid }}</div> <a href="#">
                                            <div class="fs-13 fw-bold text-truncate-1-line"> <span
                                                    class="txt-usuario">{{ $res->user->name }}</span> <span
                                                    class="ms-2 badge bg-soft-primary text-primary text-capitalize txt-inmueble">
                                                    {{ $res->res_inmueble->name }} </span> </div>
                                            <div class="fs-12 fw-normal text-truncate-1-line txt-fechas">
                                                {{ $res->fecha_inicio }} a {{ $res->fecha_fin }} </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex flex-shrink-0 align-items-center gap-3"> <a
                                        href="{{ $res->soporte_pago ? $res->getFile($res->soporte_pago) : '#' }}"
                                        class="{{ $res->soporte_pago ? 'badge bg-soft-primary text-primary' : 'text-secondary' }} text-capitalize p-2 link-soporte"
                                        target="_blank"> <i class="bi bi-paperclip"></i>
                                        {{ $res->soporte_pago ? 'Soporte de pago' : 'No hay soporte' }} </a>
                                    <div class="d-md-inline-block d-none me-3 txt-solicitud">
                                        {{ $res->fecha_solicitud->format('d M, Y') }} </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="card-footer d-flex align-items-center justify-content-end gap-2">
            <nav class="mt-3">
                <ul id="paginadorReservas" class="pagination justify-content-center"></ul>
            </nav>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar comentario</h5>
                    <button type="button" class="btn-close cerrarModal"></button>
                </div>
                <p>Nueva Interaccion</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let checkboxSeleccionado = null;
            $(".checkconfirmres").on("change", function() {
                checkboxSeleccionado = $(this);
                if ($(this).is(":checked")) {
                    let item = $(this).closest(".item-reserva");

                    let idres = $(this).data("id");
                    let action = $("#formUpdateReserva").data("action").replace(":id", idres);
                    $("#formUpdateReserva").attr("action", action);
                    $("#reserva_id").val(idres);
                    $("#usuario").val(item.find(".txt-usuario").text().trim());
                    $("#inmueble").val(item.find(".txt-inmueble").text().trim());
                    let fechas = item.find(".txt-fechas").text().trim().split("a");
                    $("#fecha_inicio").val(fechas[0].trim());
                    $("#fecha_fin").val(fechas[1].trim());
                    $("#fecha_solicitud").val(item.find(".txt-solicitud").text().trim());
                    $("#soporte_link").attr("href", item.find(".link-soporte").attr("href"));
                    $("#contacto_tel").val(item.find(".txt-contact").val());
                    $("#observacion").val("");
                    $("#confirmModal").modal("show");
                }
            });

            $(document).on("click", ".cerrarModal", function() {
                if (checkboxSeleccionado) {
                    checkboxSeleccionado.prop("checked", false);
                }

                $("#confirmModal").modal("hide");
            });


            $("#confirmBtn").click(function() {
                let id = checkboxSeleccionado.data("id");
                $("#confirmModal").modal("hide");
            });


            $("#buscadorReservas").on("keyup", function() {

                let valor = $(this).val().toLowerCase();

                $(".item-reserva").filter(function() {

                    $(this).toggle(
                        $(this).text().toLowerCase().indexOf(valor) > -1
                    );

                });

            });

            let items = $('#listaReservas .item-reserva');
            let itemsPorPagina = 5;
            let totalItems = items.length;
            let totalPaginas = Math.ceil(totalItems / itemsPorPagina);

            function mostrarPagina(pagina) {

                items.hide();

                let inicio = (pagina - 1) * itemsPorPagina;
                let fin = inicio + itemsPorPagina;

                items.slice(inicio, fin).show();

                $('#paginadorReservas li').removeClass('active');
                $('#page-' + pagina).addClass('active');
            }

            function crearPaginador() {

                let paginador = $('#paginadorReservas');
                paginador.empty();

                for (let i = 1; i <= totalPaginas; i++) {

                    paginador.append(`
                <li class="page-item" id="page-${i}">
                    <a class="page-link" href="#">${i}</a>
                </li>
            `);
                }

                $('.page-link').click(function(e) {
                    e.preventDefault();
                    let pagina = $(this).text();
                    mostrarPagina(pagina);
                });

            }

            crearPaginador();
            mostrarPagina(1);



        });
    </script>
</x-base-layout>
