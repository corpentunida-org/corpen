<x-base-layout>
    <x-warning />
    <x-success />
    <x-error />
    @section('titlepage', 'Parametros Retiros')
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center">
                        <form action="{{ route('cinco.retiros.show', ['calculoretiro' => 'ID']) }}" method="GET"
                            class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <label for="search-input" class="mb-0 me-2">Buscar:</label>
                            <input type="text" name="id" class="form-control form-control-sm" id="valueCedula"
                                placeholder="cédula" aria-controls="customerList">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <div class="row px-4 justify-content-between">
                    @foreach ($opciones as $tipo => $items)
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary">
                                @if ($tipo == 1)
                                    Beneficios
                                @elseif ($tipo == 2)
                                    Base Retención
                                @elseif ($tipo == 3)
                                    Saldos a favor
                                @endif
                            </h6>
                        </div>
                        @foreach ($items as $item)
                            <div class="form-group row mb-3 item-row">
                                <label for="input_{{ $item->id }}" class="col-sm-3 col-form-label">
                                    {{ $item->nombre }}
                                </label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control item-input" id="input_{{ $item->id }}" name="opcion[{{ $item->id }}]" value="{{ number_format($item->valdefect) }}">
                                </div>
                                <div class="col-sm-1 d-flex align-items-center">
                                    <button type="button"
                                        class="btn btn-md bg-soft-warning text-warning update-btn" style="display: none;"
                                        data-id="{{ $item->id }}">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        @if (!$loop->last)
                            <hr class="border-dashed my-4">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.item-input').on('input', function() {            
            const row = $(this).closest('.item-row');
            row.find('.update-btn').show();
        });
    </script>
</x-base-layout>
