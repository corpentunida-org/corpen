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
                <table class="table table-bordered table-hover" id="tablaReservas">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Cedula</th>
                            <th>Usuario</th>
                            <th>Inmueble</th>
                            <th>Fechas</th>
                            <th>Soporte</th>
                            <th>Solicitud</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($reservas as $res)
                            <tr>
                                <td>
                                    <input class="checkconfirmres" id="checkReserva_{{ $res->id }}" type="checkbox"
                                        data-id="{{ $res->id }}">
                                </td>

                                <td>{{ $res->nid }}</td>

                                <td>{{ $res->user->name }}</td>

                                <td>
                                    <span class="badge bg-soft-primary text-primary">
                                        {{ $res->res_inmueble->name }}
                                    </span>
                                </td>

                                <td>
                                    {{ $res->fecha_inicio }} a {{ $res->fecha_fin }}
                                </td>

                                <td>
                                    <a href="{{ $res->soporte_pago ? $res->getFile($res->soporte_pago) : '#' }}"
                                        class="{{ $res->soporte_pago ? 'badge bg-soft-primary text-primary' : 'text-secondary' }}"
                                        target="_blank">
                                        {{ $res->soporte_pago ? 'Soporte de pago' : 'No hay soporte' }}
                                    </a>
                                </td>

                                <td>
                                    {{ $res->fecha_solicitud->format('d M, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
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
        });
    </script>
</x-base-layout>
