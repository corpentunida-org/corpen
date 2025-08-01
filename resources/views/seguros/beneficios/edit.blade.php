<x-base-layout>
    <x-success />
    <x-error />
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Editar Beneficio: </span>
                    </h5>
                </div>
                <form class="row" method="post" action="" id="" novalidate>
                    @method('POST')
                    @csrf
                    <div class="row">
                        <div class="col-lg-2">
                            <label class="form-label">Descuento Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="desval" id="inputdesval" value="{{$SegBeneficios->valorDescuento}}" required>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Descuento Porcentaje</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="desval" id="inputdesval" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <label class="form-label">Observacion</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="desval" id="inputdesval" required>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 ">
                        <button type="submit" class="btn btn-md btn-primary">Filtrar</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
</x-base-layout>