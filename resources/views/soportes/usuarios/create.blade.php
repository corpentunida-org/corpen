<x-base-layout>
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
        }

        .select2-selection__rendered {
            line-height: 24px !important;
        }

        .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
    @section('titlepage', 'Crear Usuario')
    <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('soportes.usuarios.store') }}">
                    @include('soportes.usuarios.form')
                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-success" type="submit"><i class="feather-save me-2"></i> Guardar</button>
                        <a href="{{ route('soportes.usuarios.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
