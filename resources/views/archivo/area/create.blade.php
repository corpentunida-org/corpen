<x-base-layout>
    @section('titlepage', 'Crear Área')

    <div class="col-xxl-12 col-xl-12 mt-3">
        <div class="card border-top-0">
            <div class="card-header">
                <h4 class="mb-0">Nueva Área</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('archivo.area.store') }}">
                    @csrf
                    @include('archivo.area.form')

                    <div class="d-flex flex-row-reverse gap-2 mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save me-2"></i> Guardar Área
                        </button>
                        <a href="{{ route('archivo.area.index') }}" class="btn btn-light">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
