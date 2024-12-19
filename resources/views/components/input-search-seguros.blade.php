<form action="{{ route('seguros.poliza.show', ['poliza' => 'ID']) }}" method="GET"
    class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
    <label for="search-input" class="mb-0 me-2">Buscar:</label>
    <input type="text" name="id" class="form-control form-control-sm" id="valueCedula" placeholder="cédula titular"
        aria-controls="customerList">
    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
</form>