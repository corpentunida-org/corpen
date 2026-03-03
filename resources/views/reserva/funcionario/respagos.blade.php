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
        <div class="card-body" style="">
            <ul class="list-unstyled mb-0">
                @foreach ($reservascon as $res)
                    <li class="p-3 mb-3 border border-dashed rounded-3 item-reserva">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3 me-3">
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
                                    anexado coincide con el registro del pago, presione el botón azul inferior para
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
                                    <input class="form-control" id="fecha_fin" name="fecha_fin" type="text" readonly>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="soporte_link" class="col-sm-3 col-form-label">Soporte</label>
                                <div class="col-sm-9">
                                    <a id="soporte_link" target="_blank" class="btn btn-outline-primary btn-sm">
                                        Ver soporte
                                    </a>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="observacion" class="col-sm-3 col-form-label">Observación</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="observacion" name="observacion" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cerrarModal" id="cancelBtn">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="confirmBtn">Confirmar</button>
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

                console.log("Confirmado ID:", id);

                // Aquí tu acción AJAX
                /*
                $.post("/ruta", {
                    _token: "{{ csrf_token() }}",
                    id: id
                });
                */

                $("#confirmModal").modal("hide");
            });

        });
    </script>
</x-base-layout>
