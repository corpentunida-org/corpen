<div class="modal fade" id="modalCrearCuenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-university me-2 text-primary"></i>Nueva Cuenta Bancaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('contabilidad.cuentas-bancarias.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light mt-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre del Banco</label>
                            <input type="text" name="banco" class="form-control rounded-pill border-0 shadow-sm" placeholder="Ej: Bancolombia" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Cuenta</label>
                            <input type="text" name="numero_cuenta" class="form-control rounded-pill border-0 shadow-sm" placeholder="123456789" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Cuenta</label>
                            <select name="tipo_cuenta" class="form-select rounded-pill border-0 shadow-sm" required>
                                <option value="" selected disabled>Seleccione...</option>
                                <option value="Ahorros">Ahorros</option>
                                <option value="Corriente">Corriente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número Siasoft</label>
                            <input type="number" name="num_siasoft" class="form-control rounded-pill border-0 shadow-sm" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" class="form-select rounded-pill border-0 shadow-sm" required>
                                <option value="Activa" selected>Activa</option>
                                <option value="Inactiva">Inactiva</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Convenios</label>
                            <input type="text" name="convenios" class="form-control rounded-pill border-0 shadow-sm" placeholder="Nombre del convenio (opcional)">
                        </div>
                    </div>
                    <input type="hidden" name="id_user" value="{{ auth()->id() }}">
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5">Guardar Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>