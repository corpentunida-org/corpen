{{-- Busca el historial completo de crédito (activo + archivo histórico) de un asociado por cédula --}}
<form action="{{ route('creditos.credito.historial') }}" method="GET"
    class="row gx-2 align-items-center ml-2">
    <div class="col-auto">
        <label for="valueCedula" class="col-form-label mb-0">Buscar por:</label>
    </div>
    <div class="col">
        <input type="text" name="cedula" class="form-control" id="valueCedula" placeholder="cédula"
            aria-controls="customerList">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary" title="Ver historial de crédito del asociado">
            <i class="bi bi-search"></i>
        </button>
    </div>
</form>
