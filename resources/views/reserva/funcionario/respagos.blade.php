<x-base-layout>
    @section('titlepage', 'Verificar Comprobantes')
    <x-success />
    <div class="card stretch stretch-full">
        <a class="card-header">
            <h5 class="mb-0">Reservas pendientes de verificación de pago</h5>
        </a>
        <div class="card-body" style="">
            @if ($reservas->count() == 0)
                <div class="text-center">
                    <i class="bi bi-check2-circle fs-1 text-success mb-3"></i>
                    <p class="text-muted">No hay reservas pendientes de verificación de pago</p>
                </div>
            @else
                <ul class="list-unstyled mb-0">
                    @foreach ($reservas as $res)
                        <li class="p-3 mb-3 border border-dashed rounded-3 item-reserva">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3 me-3">
                                    <div class="custom-control custom-checkbox me-2">
                                        <input class="custom-control-input checkconfirmres"
                                            id="checkReserva_{{ $res->id }}" type="checkbox"
                                            data-id="{{ $res->id }}">
                                        <label class="custom-control-label c-pointer"
                                            for="checkReserva_{{ $res->id }}"></label>
                                    </div>
                                    <input type="hidden" class="txt-contact"
                                        value="{{ $res->celular }} - {{ $res->celular_respaldo }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="lh-base txt-nid">{{ $res->nid }}</div>
                                        <a href="#">
                                            <div class="fs-13 fw-bold text-truncate-1-line">
                                                <span class="txt-usuario">{{ $res->user->name }}</span>
                                                <span
                                                    class="ms-2 badge bg-soft-primary text-primary text-capitalize txt-inmueble">
                                                    {{ $res->res_inmueble->name }}
                                                </span>
                                            </div>
                                            <div class="fs-12 fw-normal text-truncate-1-line txt-fechas">
                                                {{ $res->fecha_inicio }} a {{ $res->fecha_fin }}
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex flex-shrink-0 align-items-center gap-3">
                                    <a href="{{ $res->getFile($res->soporte_pago) }}"
                                        class="badge bg-soft-primary text-primary text-capitalize p-2 link-soporte"
                                        target="_blank">
                                        <i class="bi bi-paperclip"></i> Soporte de pago
                                    </a>
                                    <div class="d-md-inline-block d-none me-3 txt-solicitud">
                                        {{ $res->fecha_solicitud->format('d M, Y') }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="card stretch stretch-full">
        <a class="card-header">
            <h5 class="mb-0">Historial de Reservas Verificadas</h5>
        </a>
        <div class="card-body" style="m-0 p-0">
            <table id="projectList" class="table">
                <thead>
                    <tr>
                        <th>Lista</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservascon as $res)
                        <tr class="border border-dashed rounded-3 item-reserva">
                            <td class="p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <!-- IZQUIERDA -->
                                    <div class="d-flex align-items-center gap-3 me-3">
                                        <div class="lh-base txt-nid">
                                            {{ $res->nid }}
                                        </div>
                                        <div>
                                            <div class="fs-13 fw-bold text-truncate-1-line">
                                                <span class="txt-usuario">{{ $res->user->name }}</span>
                                                <span
                                                    class="ms-2 badge bg-soft-primary text-primary text-capitalize txt-inmueble">
                                                    {{ $res->res_inmueble->name }}
                                                </span>
                                            </div>
                                            <div class="fs-12 fw-normal text-truncate-1-line txt-fechas">
                                                {{ $res->fecha_inicio }} a {{ $res->fecha_fin }}
                                            </div>
                                        </div>

                                    </div>
                                    <!-- DERECHA -->
                                    <div class="d-flex flex-shrink-0 align-items-center gap-3">

                                        <a href="{{ $res->getFile($res->soporte_pago) }}"
                                            class="badge bg-soft-primary text-primary text-capitalize p-2 link-soporte"
                                            target="_blank">
                                            <i class="bi bi-paperclip"></i> Soporte de pago
                                        </a>

                                        <div class="d-md-inline-block d-none me-3 txt-solicitud">
                                            {{ \Carbon\Carbon::parse($res->fecha_solicitud)->format('d M, Y') }}
                                        </div>

                                    </div>

                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Reserva</h5>
                    <button type="button" class="btn-close cerrarModal"></button>
                </div>
                <form id="formUpdateReserva" data-action="{{ route('reserva.reserva.update', ':id') }}" method="POST">
                    <div class="modal-body">
                        <div class="col-xl-12 mb-4 mb-sm-0">
                            <div class="mb-2">
                                <h6 class="fw-bold">Detalle Reserva:</h6><span class="fs-12 text-muted">Si el soporte
                                    anexado coincide con el registro del pago, presione el botón verde inferior para
                                    confirmar.</span>
                            </div>
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="reserva_id" name="reserva_id">
                            <div class="form-group row mb-3">
                                <label for="inmueble" class="col-sm-3 col-form-label">Inmueble</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="inmueble" name="inmueble" type="text" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="usuario" class="col-sm-3 col-form-label">Usuario</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="usuario" name="usuario" type="text" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="fecha_solicitud" class="col-sm-3 col-form-label">Solicitud</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="fecha_solicitud" name="fecha_solicitud"
                                        type="text" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="fecha_inicio" class="col-sm-3 col-form-label">Inicio</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="fecha_inicio" name="fecha_inicio" type="text"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="fecha_fin" class="col-sm-3 col-form-label">Fin</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="fecha_fin" name="fecha_fin" type="text"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="soporte_link" class="col-sm-3 col-form-label">Soporte</label>
                                <div class="col-sm-9">
                                    <a id="soporte_link" target="_blank" class="btn btn-outline-primary btn-sm">Ver
                                        soporte</a>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="soporte_link" class="col-sm-3 col-form-label">Contacto</label>
                                <div class="col-sm-9">
                                    <input class="form-control" id="contacto_tel" name="contacto_tel" type="text"
                                        disabled>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label for="observacion" class="col-sm-3 col-form-label">Observación</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="observacion" name="observacion" rows="2" required></textarea>
                                </div>
                            </div>

                            <div class="custom-control custom-checkbox ps-4">
                                <input type="checkbox" class="custom-control-input" id="notificar_pastor"
                                    name="notificar_pastor" value="1" checked>
                                <label class="custom-control-label c-pointer" for="notificar_pastor">Confirmar
                                    notificación al pastor por correo electrónico</label>
                            </div>
                            <input type="hidden" name="cancelar_reserva" id="cancelar_reserva" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="cancel-reservaBtn">Cancelar
                            Reserva</button>
                        <button type="submit" class="btn btn-success" id="confirmBtn">Confirmar Reserva</button>
                    </div>
                </form>
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

            $('#cancel-reservaBtn').on('click', function() {
                Swal.fire({
                    title: '¿Cancelar reserva?',
                    text: "Esta acción cancelará la reserva.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        $('#cancelar_reserva').val(1);
                        document.getElementById('formUpdateReserva').submit();
                    }
                });

            });

            $("#confirmBtn").click(function() {

                let id = checkboxSeleccionado.data("id");
                $("#confirmModal").modal("hide");
            });

            $("#formUpdateReserva").on("submit", function(e) {

                let observacion = $("#observacion");

                if ($.trim(observacion.val()) === "") {
                    observacion.addClass("is-invalid");
                    e.preventDefault();
                } else {
                    observacion.removeClass("is-invalid");
                }

            });
        });
    </script>
</x-base-layout>
