{{-- <form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}" method="GET"
    class="d-flex align-items-center gap-2 page-header-right-items-wrapper ml-2">
    <label for="search-input" class="mb-0">Buscar por:</label>
    <input type="text" name="id" class="form-control" id="valueCedula" placeholder="cédula titular"
        aria-controls="customerList">
    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
</form> --}}

<form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}" method="GET"
    class="row gx-2 align-items-center ml-2">
    <div class="col-auto">
        <label for="search-input" class="col-form-label mb-0">Buscar por:</label>
    </div>
    <div class="col">
        <input type="text" name="id" class="form-control" id="valueCedula" placeholder="cédula titular"
            aria-controls="customerList">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>