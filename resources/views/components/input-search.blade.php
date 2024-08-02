{{-- Buscador Cedulaaaa --}}
{{-- <form action="{{ route('exequial.asociados.show', ['asociado' => 'ID']) }}" method="GET"> --}}
<form action="{{ route('exequial.beneficiarios.show', ['beneficiario' => 'ID']) }}" method="GET">
    <div class="input-group no-border">
        <input type="text" name="id" value="" class="form-control" placeholder="Buscar Titular...">
        <div class="input-group-append">
            <button class="input-group-text" type="submit">
                <i class="now-ui-icons ui-1_zoom-bold"></i>
            </button>
        </div>
    </div>
</form>