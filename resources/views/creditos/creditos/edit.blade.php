<x-base-layout>
    @section('titlepage', 'Editar Crédito')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Editar Crédito de {{ $credito->tercero->nom_ter ?? $credito->mae_terceros_cod_ter }}</h5>
                    <div class="card-header-action">
                        <a href="{{ route('creditos.credito.index') }}" class="btn btn-sm btn-secondary">
                            <i class="feather-arrow-left me-1"></i> Volver a la lista
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Por favor, corrige los siguientes errores:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('creditos.credito.update', $credito) }}" method="POST">
                        @method('PUT')
                        @include('creditos.creditos._form', ['buttonText' => 'Actualizar Crédito'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
